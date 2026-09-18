<?php

namespace App\Services;

use App\Models\ContractTemplate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Gère l'upload, le versioning et la détection de variables des templates DOCX.
 */
class DocxTemplateManager
{
    public function __construct(
        private ContractDocxRenderer $renderer,
    ) {}

    /**
     * Valide, stocke et enregistre un nouveau template DOCX.
     * Incrémente la version et archive l'ancien fichier si nécessaire.
     *
     * @return string[]  Liste des variables détectées (sans accolades)
     */
    public function upload(UploadedFile $file, ContractTemplate $template): array
    {
        $this->validateDocx($file);

        $oldPath = $template->docx_template_path;
        $version = ($template->docx_version ?? 0) + 1;

        $filename = 'template_' . $template->id . '_v' . $version . '_' . time() . '.docx';

        // Le disque 'local' est configuré avec 'throw' => false : en cas d'échec
        // d'écriture (droits, quota), storeAs() renvoie false sans lever d'exception.
        $storagePath = $file->storeAs('docx-templates', $filename, 'local');
        if ($storagePath === false) {
            $root = Storage::disk('local')->path('docx-templates');
            throw new \RuntimeException(
                "Écriture impossible dans $root — vérifiez les droits du dossier storage/ "
                . '(755 sur les dossiers, 644 sur les fichiers) et l\'espace disque disponible.'
            );
        }

        // On demande son chemin au disque plutôt que de supposer storage/app :
        // la racine du disque peut différer selon la configuration de l'hébergement.
        $absPath = Storage::disk('local')->path($storagePath);

        // Détecter les variables avant de toucher à la BDD
        // (si le DOCX est invalide, on s'arrête sans modifier le template existant)
        try {
            $vars = $this->detectVariables($absPath);
        } catch (\Throwable $e) {
            // On ne supprime pas le fichier : quand l'échec vient de l'environnement
            // (droits, quota, open_basedir), il est la seule pièce à conviction.
            // On le met de côté sous .failed pour ne pas le confondre avec un
            // template valide, et on trace son état dans les logs.
            $failed = $absPath . '.failed';
            @rename($absPath, $failed);

            \Illuminate\Support\Facades\Log::error(
                'DocxTemplateManager: détection des variables échouée — ' . $e->getMessage(),
                [
                    'fichier_conserve' => is_file($failed) ? $failed : $absPath,
                    'taille'           => is_file($failed) ? filesize($failed)
                                        : (is_file($absPath) ? filesize($absPath) : null),
                ]
            );

            throw $e;
        }

        // Archiver l'ancien fichier avant d'écraser la référence en BDD
        if ($oldPath) {
            try {
                $this->archiveOld($oldPath, $template->id, $version - 1);
            } catch (\Throwable $e) {
                // Archivage non critique : on logue mais on ne bloque pas l'upload
                \Illuminate\Support\Facades\Log::warning(
                    'DocxTemplateManager: archivage ancien template échoué — ' . $e->getMessage()
                );
            }
        }

        $template->update([
            'docx_template_path' => $storagePath,
            'docx_version'       => $version,
            'docx_detected_vars' => $vars,
        ]);

        return $vars;
    }

    /**
     * Extrait toutes les variables {balise} présentes dans un fichier DOCX.
     * Défragmente d'abord le XML Word pour reconstituer les balises éclatées.
     *
     * @return string[]  ex: ['reference', 'nom_client', 'devise']
     */
    public function detectVariables(string $docxAbsPath): array
    {
        $zip  = new ZipArchive();
        $code = $zip->open($docxAbsPath);
        if ($code !== true) {
            throw new \RuntimeException(
                "Impossible d'ouvrir le DOCX : $docxAbsPath — " . self::describeZipError($code, $docxAbsPath)
            );
        }

        $targets = ['word/document.xml'];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#^word/(header|footer)\d+\.xml$#', $name)) {
                $targets[] = $name;
            }
        }

        $vars = [];
        foreach ($targets as $target) {
            $xml = $zip->getFromName($target);
            if ($xml === false) {
                continue;
            }

            $xml = $this->renderer->defragment($xml);

            // Concatène sans séparateur pour détecter les variables encore fragmentées
            preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/', $xml, $textMatches);
            $text = implode('', $textMatches[1]);

            preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $text, $varMatches);
            $vars = array_merge($vars, $varMatches[1]);
        }

        $zip->close();

        return array_values(array_unique($vars));
    }

    /**
     * Traduit un code d'erreur ZipArchive en diagnostic exploitable,
     * complété par l'état réel du fichier sur le disque.
     */
    public static function describeZipError(int $code, string $path): string
    {
        $reasons = [
            ZipArchive::ER_NOENT  => 'le fichier est introuvable à cet emplacement',
            ZipArchive::ER_NOZIP  => "le fichier n'est pas une archive ZIP valide (DOCX corrompu ou écriture incomplète)",
            ZipArchive::ER_INCONS => 'archive incohérente (fichier tronqué — quota disque atteint ?)',
            ZipArchive::ER_READ   => 'lecture impossible (droits insuffisants)',
            ZipArchive::ER_OPEN   => "ouverture refusée par le système (droits ou open_basedir)",
            ZipArchive::ER_MEMORY => 'mémoire insuffisante',
            ZipArchive::ER_CRC    => 'erreur de somme de contrôle (fichier altéré)',
        ];

        $dir    = dirname($path);
        $exists = file_exists($path);

        // L'état réel du fichier prime : selon les builds PHP, ZipArchive renvoie
        // ER_READ plutôt que ER_NOENT pour un fichier absent, ce qui induit en erreur.
        if (!$exists) {
            $reason = is_dir($dir)
                ? "le fichier n'a pas été écrit à cet emplacement"
                : "le dossier de destination n'existe pas";
        } elseif (!is_readable($path)) {
            $reason = 'fichier présent mais non lisible (droits insuffisants)';
        } elseif (filesize($path) === 0) {
            $reason = 'fichier vide (écriture interrompue — quota disque ?)';
        } else {
            $reason = $reasons[$code] ?? "code d'erreur ZipArchive $code";
        }

        $state = [];
        $state[] = $exists ? 'fichier présent' : 'fichier absent';
        if ($exists) {
            $state[] = 'taille ' . filesize($path) . ' octets';
            $state[] = is_readable($path) ? 'lisible' : 'NON lisible';
        }
        $state[] = is_dir($dir)
            ? 'dossier parent ' . (is_writable($dir) ? 'accessible en écriture' : 'NON accessible en écriture')
            : 'dossier parent absent';

        return $reason . ' [' . implode(', ', $state) . ']';
    }

    // ── Privé ─────────────────────────────────────────────────────────────────

    private function validateDocx(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext !== 'docx') {
            throw new \InvalidArgumentException('Le fichier doit être un .docx (reçu : .' . $ext . ')');
        }

        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new \InvalidArgumentException('Le fichier DOCX est corrompu ou invalide.');
        }

        $valid = $zip->locateName('word/document.xml') !== false;
        $zip->close();

        if (!$valid) {
            throw new \InvalidArgumentException('Le fichier n\'est pas un DOCX valide (word/document.xml absent).');
        }
    }

    private function archiveOld(string $oldPath, int $templateId, int $oldVersion): void
    {
        $src = Storage::disk('local')->path($oldPath);
        if (!file_exists($src)) {
            return;
        }

        $archiveDir = Storage::disk('local')->path('docx-templates/archives');
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0755, true);
        }

        $dest = $archiveDir . '/template_' . $templateId . '_v' . $oldVersion . '.docx';
        rename($src, $dest);
    }
}
