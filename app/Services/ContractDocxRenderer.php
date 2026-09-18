<?php

namespace App\Services;

use App\Models\ContractTemplate;
use App\Models\LoanRequest;
use ZipArchive;

/**
 * DocxRenderer — Moteur de génération DOCX.
 *
 * Pipeline :
 *  1. Copie le template DOCX vers un fichier temporaire
 *  2. Ouvre le ZIP en écriture
 *  3. Défragmente les balises Word éclatées sur plusieurs <w:r> runs
 *  4. Substitue les variables {balise} → valeur dans document.xml + header*.xml + footer*.xml
 *  5. Valide l'absence de balises résiduelles
 *  6. Ferme le ZIP → DOCX final
 *  7. Retourne le chemin absolu du fichier généré
 */
class ContractDocxRenderer
{
    private ContractVariableResolver $resolver;

    public function __construct(ContractVariableResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Génère le DOCX pour un LoanRequest donné.
     * Retourne le chemin absolu du fichier DOCX généré.
     */
    public function generate(LoanRequest $loan, ContractTemplate $template, string $locale = 'fr'): string
    {
        $docxPath = $this->resolveTemplatePath($template);
        $vars     = $this->resolver->resolve($loan, $locale);

        $outputPath = $this->buildOutputPath($loan->reference, $locale);
        return $this->render($docxPath, $vars, $outputPath);
    }

    /**
     * Génère un DOCX d'aperçu avec des données de démonstration.
     * Retourne le chemin absolu du fichier DOCX généré.
     */
    public function generatePreview(ContractTemplate $template, string $locale = 'fr'): string
    {
        $docxPath = $this->resolveTemplatePath($template);
        $vars     = $this->resolver->resolveSample($locale);

        $outputPath = $this->buildOutputPath('preview_' . $template->id . '_' . uniqid(), $locale);
        return $this->render($docxPath, $vars, $outputPath);
    }

    /**
     * Noyau : copie le template, défragmente, substitue, valide.
     *
     * @param  string  $templateAbsPath  Chemin absolu du DOCX template
     * @param  array   $vars             [{balise} => valeur_XML_escaped]
     * @param  string  $outputAbsPath    Chemin absolu de sortie
     */
    public function render(string $templateAbsPath, array $vars, string $outputAbsPath): string
    {
        if (!file_exists($templateAbsPath)) {
            throw new \RuntimeException("Template DOCX introuvable : $templateAbsPath");
        }

        // Assurer que le dossier de sortie existe
        $dir = dirname($outputAbsPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        copy($templateAbsPath, $outputAbsPath);

        $zip = new ZipArchive();
        if ($zip->open($outputAbsPath) !== true) {
            throw new \RuntimeException("Impossible d'ouvrir le DOCX : $outputAbsPath");
        }

        try {
            $this->processZip($zip, $vars);
        } finally {
            $zip->close();
        }

        if (!file_exists($outputAbsPath) || filesize($outputAbsPath) < 10_000) {
            throw new \RuntimeException("DOCX généré invalide : $outputAbsPath");
        }

        return $outputAbsPath;
    }

    // ── Traitement interne du ZIP ─────────────────────────────────────────────

    private function processZip(ZipArchive $zip, array $vars): void
    {
        // Fichiers XML à traiter dans le DOCX
        $targets = ['word/document.xml'];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#^word/(header|footer)\d+\.xml$#', $name)) {
                $targets[] = $name;
            }
        }

        foreach ($targets as $target) {
            $xml = $zip->getFromName($target);
            if ($xml === false) {
                continue;
            }

            $xml = $this->defragment($xml);
            $xml = $this->substitute($xml, $vars);
            $xml = $this->substituteAcrossRuns($xml, $vars);

            if ($target === 'word/document.xml') {
                $this->assertNoResidualTags($xml, $vars);
            }

            $zip->addFromString($target, $xml);
        }
    }

    // ── Défragmentation ───────────────────────────────────────────────────────

    /**
     * Reconstruit les balises {variable} fragmentées en plusieurs <w:r> runs Word.
     *
     * Patterns couverts (préfixe = texte avant { dans le même <w:t>) :
     *   D)  { | partial | rest}     → 3 runs, nom fragmenté, { seul au début du run
     *   A)  { | varname | }         → 3 runs, nom complet, { seul au début du run
     *   B)  {varname | }            → 2 runs, {varname au début du run, } seul
     *   C)  { | varname}            → 2 runs, { seul au début du run, nom+} ensemble
     *   EH) préfixe{ | varname | } → 3 runs, { en milieu de run (3 façons)
     *   EG) préfixe{varname | }    → 2 runs, {varname en milieu de run, } seul
     *   EF) préfixe{ | varname}    → 2 runs, { en milieu de run, nom+} ensemble
     *   ED) préfixe{ | p1 | p2}    → 3 runs, nom fragmenté, { en milieu de run
     */
    public function defragment(string $xml): string
    {
        $v = '[a-zA-Z_][a-zA-Z0-9_]*';

        $rpr = '(?:<w:rPr>[^<]*(?:<(?!/w:rPr>)[^<]*)*</w:rPr>)?';
        // Entre deux </w:r> et <w:r> : autorise tout élément intermédiaire
        // (ex: <w:proofErr/>, <w:bookmarkStart/>, commentaires…) sans franchir <w:p>
        $run = '</w:t></w:r>(?:(?!<w:r\b|<w:p\b)[\s\S])*?<w:r\b[^>]*>' . $rpr . '<w:t[^>]*>';
        // Texte avant { dans le même <w:t> (ne peut pas contenir < ni {)
        $pre = '[^<{]*';

        // ── Patterns A-D : { en DÉBUT de run ────────────────────────────────

        // Pattern D : { | partial_name | rest_name}
        $xml = preg_replace(
            '#(<w:t[^>]*>)\{' . $run . '([a-zA-Z_][a-zA-Z0-9_]*)' . $run . '([a-zA-Z0-9_]+)\}#s',
            '$1{$2$3}',
            $xml
        );

        // Pattern A : { | varname | }
        $xml = preg_replace(
            '#(<w:t[^>]*>)\{' . $run . '(' . $v . ')' . $run . '\}#s',
            '$1{$2}',
            $xml
        );

        // Pattern B : {varname | }
        $xml = preg_replace(
            '#(<w:t[^>]*>)(\{' . $v . ')' . $run . '\}#s',
            '$1$2}',
            $xml
        );

        // Pattern C : { | varname}
        $xml = preg_replace(
            '#(<w:t[^>]*>)\{' . $run . '(' . $v . '\}\s*)#s',
            '$1{$2',
            $xml
        );

        // ── Patterns E* : { en MILIEU de run (précédé de texte) ─────────────

        // Pattern ED : préfixe{ | partial | rest}  (nom fragmenté, { en milieu)
        $xml = preg_replace(
            '#(<w:t[^>]*>' . $pre . ')\{' . $run . '([a-zA-Z_][a-zA-Z0-9_]*)' . $run . '([a-zA-Z0-9_]+)\}#s',
            '$1{$2$3}',
            $xml
        );

        // Pattern EH : préfixe{ | varname | }  (3 runs, { en milieu)
        $xml = preg_replace(
            '#(<w:t[^>]*>' . $pre . ')\{' . $run . '(' . $v . ')' . $run . '\}#s',
            '$1{$2}',
            $xml
        );

        // Pattern EG : préfixe{varname | }  (2 runs, {varname en milieu, } seul)
        $xml = preg_replace(
            '#(<w:t[^>]*>' . $pre . ')(\{' . $v . ')' . $run . '\}#s',
            '$1$2}',
            $xml
        );

        // Pattern EF : préfixe{ | varname}  (2 runs, { en milieu, nom+} ensemble)
        $xml = preg_replace(
            '#(<w:t[^>]*>' . $pre . ')\{' . $run . '(' . $v . '\}\s*)#s',
            '$1{$2',
            $xml
        );

        return $xml;
    }

    // ── Substitution ──────────────────────────────────────────────────────────

    /**
     * Remplace les balises {xxx} par leurs valeurs dans le XML.
     * Double passe pour résoudre les balises imbriquées (ex: {borrower_desc} contient {nom_client}).
     */
    private function substitute(string $xml, array $vars): string
    {
        $xml = str_replace(array_keys($vars), array_values($vars), $xml);
        $xml = str_replace(array_keys($vars), array_values($vars), $xml);
        return $xml;
    }

    /**
     * Substitution de dernier recours pour les variables dont le NOM lui-même
     * est fragmenté à travers plusieurs <w:t> adjacents (ex: "{nom_cl" + "ient}").
     *
     * Construit le texte virtuel par concaténation de tous les <w:t>, localise
     * chaque {variable} et redistribue la valeur dans les runs concernés.
     */
    private function substituteAcrossRuns(string $xml, array $vars): string
    {
        preg_match_all('/(<w:t(?:[^>]*)>)([^<]*)(<\/w:t>)/', $xml, $m, PREG_OFFSET_CAPTURE);
        $n = count($m[0]);
        if (!$n) return $xml;

        $texts   = [];
        $xmlPos  = [];
        $virtual = '';
        $vStart  = [];

        for ($i = 0; $i < $n; $i++) {
            $vStart[$i] = strlen($virtual);
            $texts[$i]  = $m[2][$i][0];
            $xmlPos[$i] = $m[2][$i][1];
            $virtual   .= $texts[$i];
        }

        $patches = [];

        foreach ($vars as $key => $value) {
            $kLen = strlen($key);
            $vp   = 0;
            while (($vp = strpos($virtual, $key, $vp)) !== false) {
                $ve = $vp + $kLen;

                $hit = [];
                for ($i = 0; $i < $n; $i++) {
                    $rs = $vStart[$i];
                    $re = $rs + strlen($texts[$i]);
                    if ($rs < $ve && $re > $vp) $hit[] = $i;
                }

                if (count($hit) > 1) {
                    foreach ($hit as $idx => $ri) {
                        $rs    = $vStart[$ri];
                        $from  = max(0, $vp - $rs);
                        $to    = min(strlen($texts[$ri]), $ve - $rs);
                        $patch = substr($texts[$ri], 0, $from)
                               . ($idx === 0 ? $value : '')
                               . substr($texts[$ri], $to);
                        $patches[] = [$xmlPos[$ri], strlen($texts[$ri]), $patch];
                    }
                }
                $vp++;
            }
        }

        if (empty($patches)) return $xml;

        usort($patches, fn($a, $b) => $b[0] - $a[0]);

        $seen = [];
        foreach ($patches as [$pos, $len, $text]) {
            if (!isset($seen[$pos])) {
                $seen[$pos] = true;
                $xml = substr($xml, 0, $pos) . $text . substr($xml, $pos + $len);
            }
        }

        return $xml;
    }

    // ── Validation ────────────────────────────────────────────────────────────

    /**
     * Lève une exception si des balises {xxx} connues du resolver restent non substituées.
     * On concatène sans séparateur pour détecter les fragments encore éclatés sur
     * plusieurs <w:t>, mais on n'alerte que sur les variables présentes dans $vars
     * (les balises inconnues du template sont ignorées silencieusement).
     */
    private function assertNoResidualTags(string $xml, array $vars): void
    {
        preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/', $xml, $m);
        $texts = implode('', $m[1]);

        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $texts, $found);
        $residual = array_filter(
            array_unique($found[0] ?? []),
            fn($tag) => isset($vars[$tag])
        );

        if (!empty($residual)) {
            throw new \RuntimeException(
                'Génération DOCX impossible : Balises DOCX non résolues : ' . implode(', ', $residual)
                . '. Vérifiez que les données du dossier sont complètes.'
            );
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolveTemplatePath(ContractTemplate $template): string
    {
        if (!$template->docx_template_path) {
            throw new \RuntimeException(
                "Aucun template DOCX uploadé pour le modèle \"{$template->name}\"."
            );
        }

        // On demande le chemin au disque plutôt que de supposer storage/app.
        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($template->docx_template_path);

        if (!file_exists($path)) {
            $dir = dirname($path);
            throw new \RuntimeException(
                "Fichier DOCX template introuvable : {$template->docx_template_path}"
                . " — attendu à $path ["
                . (is_dir($dir)
                    ? 'dossier présent, ' . count(glob($dir . '/*.docx') ?: []) . ' fichier(s) .docx dedans'
                    : 'dossier absent')
                . ']. Le fichier a probablement été supprimé, ou l\'upload a échoué sans être détecté.'
            );
        }

        return $path;
    }

    private function buildOutputPath(string $reference, string $locale): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $reference);
        $dir  = storage_path('app/generated-contracts');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . DIRECTORY_SEPARATOR . 'contract_' . $safe . '_' . $locale . '.docx';
    }
}
