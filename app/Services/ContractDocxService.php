<?php

namespace App\Services;

use App\Models\LoanRequest;
use App\Models\ContractTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Gère les templates DOCX : extraction des balises, substitution et conversion PDF via LibreOffice.
 */
class ContractDocxService
{
    public function __construct(
        private ContractService        $contractService,
        private GroqDocxRendererService $groqRenderer,
    ) {}

    // ── Extraction des balises ────────────────────────────────────────────────

    public function extractTags(string $docxPath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return [];
        }

        $xmlParts = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_starts_with($name, 'word/') && str_ends_with($name, '.xml')) {
                $xmlParts[] = $zip->getFromIndex($i);
            }
        }
        $zip->close();

        $allXml  = implode(' ', $xmlParts);
        $cleaned = strip_tags($allXml);

        preg_match_all('/\{([a-zA-Z][a-zA-Z0-9_]*)\}/', $cleaned, $matches);

        return array_values(array_unique($matches[0] ?? []));
    }

    // ── Substitution des balises ──────────────────────────────────────────────

    public function applySubstitutions(string $templatePath, array $vars): string
    {
        $tempDir  = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/contract_' . Str::uuid() . '.docx';
        copy($templatePath, $tempPath);

        $zip = new ZipArchive();
        if ($zip->open($tempPath) !== true) {
            throw new \RuntimeException("Impossible d'ouvrir le DOCX : $tempPath");
        }

        $entries = ['word/document.xml', 'word/header1.xml', 'word/header2.xml',
                    'word/footer1.xml',  'word/footer2.xml'];

        foreach ($entries as $entry) {
            $xml = $zip->getFromName($entry);
            if ($xml === false) continue;

            $xml = $this->mergeAdjacentRuns($xml);
            $xml = $this->substituteInXml($xml, $vars);
            $zip->addFromString($entry, $xml);
        }

        $zip->close();
        return $tempPath;
    }

    private function substituteInXml(string $xml, array $vars): string
    {
        $search  = array_keys($vars);
        $replace = array_map(
            fn($v) => htmlspecialchars((string)$v, ENT_XML1 | ENT_QUOTES, 'UTF-8'),
            array_values($vars)
        );
        return str_replace($search, $replace, $xml);
    }

    private function mergeAdjacentRuns(string $xml): string
    {
        // Pass 1: join adjacent <w:t> within the same run
        $xml = preg_replace('/<\/w:t>(<w:t(?:\s[^>]*)?>)/', '$1', $xml) ?? $xml;

        // Pass 2: merge consecutive runs with no rPr between them
        $xml = preg_replace('/<\/w:t><\/w:r><w:r><w:t(?:\s[^>]*)?>/', '', $xml) ?? $xml;

        // Pass 3: per-paragraph repair of {tags} split across runs (Word adds <w:rPr>
        // like <w:lang w:val="fr-FR"/> between {, devise, } causing 3 separate runs)
        $xml = (string) preg_replace_callback(
            '/<w:p[ >].*?<\/w:p>/s',
            fn($m) => $this->repairSplitTagsInParagraph($m[0]),
            $xml
        );

        return $xml;
    }

    private function repairSplitTagsInParagraph(string $pXml): string
    {
        if (!str_contains($pXml, '{') || !str_contains($pXml, '}')) return $pXml;

        // Iterate until stable (max 10 passes to handle multiple split tags per paragraph)
        for ($pass = 0; $pass < 10; $pass++) {
            preg_match_all('/<w:r\b[^>]*>.*?<\/w:r>/s', $pXml, $rm);
            $runs   = $rm[0] ?? [];
            $merged = false;

            for ($i = 0, $n = count($runs); $i < $n; $i++) {
                preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $runs[$i], $tM);
                $text = implode('', $tM[1]);

                if (!str_contains($text, '{')) continue;
                if (preg_match('/\{[a-zA-Z][a-zA-Z0-9_]*\}/', $text)) continue; // already complete

                // Try merging with subsequent runs until the tag closes
                $combined = $text;
                for ($j = $i + 1; $j < min($n, $i + 8); $j++) {
                    preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $runs[$j], $tM2);
                    $combined .= implode('', $tM2[1]);

                    if (preg_match('/\{[a-zA-Z][a-zA-Z0-9_]*\}/', $combined)) {
                        // Keep rPr of run $i, combine all text into one run
                        preg_match('/<w:rPr>.*?<\/w:rPr>/s', $runs[$i], $rPrM);
                        $mergedRun = '<w:r>'
                            . ($rPrM[0] ?? '')
                            . '<w:t xml:space="preserve">'
                            . htmlspecialchars($combined, ENT_XML1, 'UTF-8')
                            . '</w:t></w:r>';

                        $origSeq = implode('', array_slice($runs, $i, $j - $i + 1));
                        $pXml    = str_replace($origSeq, $mergedRun, $pXml);
                        $merged  = true;
                        break 2; // restart scan
                    }
                }
            }

            if (!$merged) break;
        }

        return $pXml;
    }

    // ── Conversion LibreOffice ────────────────────────────────────────────────

    public function convertToPdf(string $docxPath): string
    {
        $binary    = $this->resolveLoBinary();
        $outputDir = storage_path('app/temp');

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        if ($binary === null) {
            throw new \RuntimeException(
                "LibreOffice introuvable.\n" .
                "Exécutez : php artisan docx:install-libreoffice"
            );
        }

        $cmd = sprintf(
            '"%s" --headless --convert-to pdf --outdir "%s" "%s" 2>&1',
            $binary,
            $outputDir,
            $docxPath
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException(
                "Échec de la conversion LibreOffice (code $exitCode) :\n" . implode("\n", $output)
            );
        }

        $pdfPath = $outputDir . '/' . pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';

        if (!file_exists($pdfPath)) {
            throw new \RuntimeException("PDF non généré. Chemin attendu : $pdfPath");
        }

        return $pdfPath;
    }

    // ── Pipeline complet ──────────────────────────────────────────────────────

    public function generatePdf(LoanRequest $loan, ContractTemplate $template): string
    {
        $vars         = $this->contractService->getVariables($loan);
        $templatePath = Storage::path($template->docx_path);
        $docxOut      = $this->applySubstitutions($templatePath, $vars);

        try {
            // LibreOffice preferred when available
            if ($this->resolveLoBinary()) {
                return $this->convertToPdf($docxOut);
            }

            // Fallback: phpoffice HTML → dompdf
            $html = $this->convertWithPhpWord($docxOut);

            // Inject saved watermark CSS from admin edits if present
            if (!empty($template->content) && stripos($template->content, '<html') !== false) {
                $savedCss = $this->extractHtmlStyles($template->content);
                if (preg_match('/\.crx-wm-user\s*\{[^}]+\}/s', $savedCss, $wmM)) {
                    $html = preg_replace('/<\/style>/i', $wmM[0] . '</style>', $html, 1) ?? $html;
                    $html = preg_replace('/<body([^>]*)>/i', '<body$1><div class="crx-wm-user"></div>', $html, 1) ?? $html;
                }
            }

            return $this->savePdfFromHtml($html, $loan->reference ?? uniqid());

        } finally {
            @unlink($docxOut);
        }
    }

    private function savePdfFromHtml(string $html, string $reference): string
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'DejaVu Sans',
            ]);

        $filename = 'contract_' . $reference . '.pdf';
        $path     = 'contracts/' . $filename;

        Storage::put($path, $pdf->output());

        return storage_path('app/' . $path);
    }

    /**
     * Generate a PDF from the saved edited HTML (for template download).
     * Images are re-injected from the original DOCX, then CSS is cleaned for dompdf compatibility.
     */
    public function generatePdfFromSavedHtml(string $savedHtml, string $docxPath, string $reference = 'preview'): string
    {
        $html = $this->reInjectImages($savedHtml, $docxPath);
        $html = $this->cleanHtmlForDomPdf($html);

        return $this->savePdfFromHtml($html, $reference);
    }

    // ── Téléchargement DOCX avec modifications du preview ─────────────────────

    /**
     * Crée un DOCX de téléchargement à partir de l'original (balises préservées)
     * en y intégrant le watermark utilisateur et les images insérées via le preview.
     * Retourne le chemin du fichier temporaire (à supprimer après envoi).
     */
    public function createDownloadDocx(string $originalDocx, ?string $savedHtml): string
    {
        $userWm   = $savedHtml ? $this->extractUserWatermarkData($savedHtml) : null;
        $userImgs = $savedHtml ? $this->extractUserAbsImages($savedHtml)     : [];

        if (!$userWm && empty($userImgs)) {
            // Aucune modification → copie simple pour deleteFileAfterSend
            $copy = sys_get_temp_dir() . '/' . uniqid('docx_dl_') . '.docx';
            copy($originalDocx, $copy);
            return $copy;
        }

        $newPath = sys_get_temp_dir() . '/' . uniqid('docx_dl_') . '.docx';
        copy($originalDocx, $newPath);

        $zip = new ZipArchive();
        if ($zip->open($newPath) !== true) {
            return $newPath;
        }

        // ── Watermark dans le header ──────────────────────────────────────────
        if ($userWm) {
            $this->embedWatermarkInDocxZip($zip, $userWm);
        }

        // ── Images positionnées dans le body ──────────────────────────────────
        if (!empty($userImgs)) {
            $this->embedAnchoredImagesInDocxZip($zip, $userImgs);
        }

        $zip->close();
        return $newPath;
    }

    /** Extrait le data URI du watermark (.crx-wm-user CSS background). */
    private function extractUserWatermarkData(string $html): ?array
    {
        $css = $this->extractHtmlStyles($html);
        if (preg_match(
            '/\.crx-wm-user\s*\{[^}]*background[^:]*:[^;]*url\s*\(\s*["\']?(data:(image\/[a-z+]+);base64,([A-Za-z0-9+\/=]+))["\']?\s*\)/s',
            $css, $m
        )) {
            return ['mime' => $m[2], 'b64' => $m[3]];
        }
        return null;
    }

    /**
     * Extrait les images absolues user (crx-user-abs).
     * Chaque entrée : ['b64', 'mime', 'leftPx', 'topPx', 'widthPx', 'heightPx'].
     */
    private function extractUserAbsImages(string $html): array
    {
        $body = $this->extractHtmlBody($html);
        $imgs = [];

        // Cherche les <div class="crx-user-abs" ...>...<img ...>...</div>
        preg_match_all(
            '/<div[^>]*class="crx-user-abs"[^>]*>(.*?)<\/div>/s',
            $body, $wrappers
        );

        foreach ($wrappers[1] as $inner) {
            preg_match_all(
                '/<img[^>]*src=["\']data:(image\/[a-z+]+);base64,([A-Za-z0-9+\/=]+)["\'][^>]*>/i',
                $inner, $imgMatches, PREG_SET_ORDER
            );
            foreach ($imgMatches as $imgM) {
                $style = '';
                if (preg_match('/style=["\']([^"\']+)["\']/', $imgM[0], $sm)) $style = $sm[1];

                $left = $top = 0;
                $w    = 300; $h = 200;
                if (preg_match('/left\s*:\s*([\d.]+)px/i',   $style, $lm)) $left = (float)$lm[1];
                if (preg_match('/top\s*:\s*([\d.]+)px/i',    $style, $tm)) $top  = (float)$tm[1];
                if (preg_match('/width\s*:\s*([\d.]+)px/i',  $style, $wm)) $w    = (float)$wm[1];
                if (preg_match('/height\s*:\s*([\d.]+)px/i', $style, $hm)) $h    = (float)$hm[1];

                $imgs[] = [
                    'mime'     => $imgM[1],
                    'b64'      => $imgM[2],
                    'leftPx'   => $left,
                    'topPx'    => $top,
                    'widthPx'  => $w,
                    'heightPx' => $h,
                ];
            }
        }

        return $imgs;
    }

    /** Intègre le watermark dans word/header1.xml via une forme VML. */
    private function embedWatermarkInDocxZip(ZipArchive $zip, array $wm): void
    {
        $ext     = str_contains($wm['mime'], 'jpeg') ? 'jpg' : 'png';
        $imgName = 'wm_crx_user.' . $ext;
        $zip->addFromString('word/media/' . $imgName, base64_decode($wm['b64']));

        // Relation dans header1.xml.rels
        $relsPath = 'word/_rels/header1.xml.rels';
        $relsXml  = $zip->getFromName($relsPath) ?: '';
        if (!$relsXml) {
            $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                     . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"/>';
        }
        preg_match_all('/Id="rId(\d+)"/', $relsXml, $m);
        $nextId = $m[1] ? max(array_map('intval', $m[1])) + 1 : 10;
        $wmRid  = 'rId' . $nextId;

        $relsXml = str_replace(
            '</Relationships>',
            '<Relationship Id="' . $wmRid . '" '
            . 'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" '
            . 'Target="media/' . $imgName . '"/></Relationships>',
            $relsXml
        );
        // Si balise auto-fermante : <Relationships .../>
        if (!str_contains($relsXml, '</Relationships>')) {
            $relsXml = str_replace(
                '/>',
                '><Relationship Id="' . $wmRid . '" '
                . 'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" '
                . 'Target="media/' . $imgName . '"/></Relationships>',
                $relsXml
            );
        }
        $zip->addFromString($relsPath, $relsXml);

        // Forme VML dans header1.xml
        $hdrPath = 'word/header1.xml';
        $hdrXml  = $zip->getFromName($hdrPath) ?: '';

        $wmShape =
            '<w:p><w:pPr><w:pStyle w:val="Header"/></w:pPr>'
            . '<w:r><w:pict>'
            . '<v:shape id="_crx_wm_user" type="#_x0000_t75"'
            . ' style="position:absolute;margin-left:0;margin-top:0;'
            . 'width:595pt;height:842pt;z-index:-251658752;'
            . 'mso-position-horizontal:center;mso-position-horizontal-relative:page;'
            . 'mso-position-vertical:center;mso-position-vertical-relative:page"'
            . ' o:allowincell="f">'
            . '<v:imagedata r:id="' . $wmRid . '" o:title="" gain="0.20000"/>'
            . '</v:shape></w:pict></w:r></w:p>';

        if ($hdrXml) {
            // Ajouter avant le premier <w:p> existant
            $hdrXml = preg_replace('/(<w:p\b)/', $wmShape . '$1', $hdrXml, 1) ?? $hdrXml;
        } else {
            $hdrXml =
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<w:hdr'
                . ' xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
                . ' xmlns:v="urn:schemas-microsoft-com:vml"'
                . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
                . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . $wmShape
                . '<w:p><w:pPr><w:pStyle w:val="Header"/></w:pPr></w:p>'
                . '</w:hdr>';
        }
        $zip->addFromString($hdrPath, $hdrXml);
    }

    /**
     * Intègre les images absolues en tant que wp:anchor dans word/document.xml.
     * 1 px preview (96dpi, page 794px) ≈ 9525 EMU.
     */
    private function embedAnchoredImagesInDocxZip(ZipArchive $zip, array $userImgs): void
    {
        $docXml  = $zip->getFromName('word/document.xml') ?: '';
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels') ?: '';
        if (!$docXml || !$relsXml) return;

        preg_match_all('/Id="rId(\d+)"/', $relsXml, $m);
        $nextId = $m[1] ? max(array_map('intval', $m[1])) + 1 : 20;

        // 96 dpi : 1px = 914400/96 = 9525 EMU
        $emuPerPx = 9525;

        $insertXml = '';
        foreach ($userImgs as $i => $img) {
            $ext     = str_contains($img['mime'], 'jpeg') ? 'jpg' : 'png';
            $imgName = 'crx_abs_img_' . ($i + 1) . '.' . $ext;
            $zip->addFromString('word/media/' . $imgName, base64_decode($img['b64']));

            $rid = 'rId' . ($nextId + $i);
            $relsXml = str_replace(
                '</Relationships>',
                '<Relationship Id="' . $rid . '"'
                . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image"'
                . ' Target="media/' . $imgName . '"/></Relationships>',
                $relsXml
            );

            $cx  = intval($img['widthPx']  * $emuPerPx);
            $cy  = intval($img['heightPx'] * $emuPerPx);
            $posX = intval($img['leftPx']  * $emuPerPx);
            $posY = intval($img['topPx']   * $emuPerPx);
            $uid  = 5000 + $i;

            $insertXml .=
                '<w:p><w:r><w:drawing>'
                . '<wp:anchor distT="0" distB="0" distL="0" distR="0"'
                . ' simplePos="0" relativeHeight="251659264" behindDoc="0"'
                . ' locked="0" layoutInCell="1" allowOverlap="1"'
                . ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">'
                . '<wp:simplePos x="0" y="0"/>'
                . '<wp:positionH relativeFrom="page"><wp:posOffset>' . $posX . '</wp:posOffset></wp:positionH>'
                . '<wp:positionV relativeFrom="page"><wp:posOffset>' . $posY . '</wp:posOffset></wp:positionV>'
                . '<wp:extent cx="' . $cx . '" cy="' . $cy . '"/>'
                . '<wp:effectExtent l="0" t="0" r="0" b="0"/>'
                . '<wp:wrapNone/>'
                . '<wp:docPr id="' . $uid . '" name="CrxImg' . ($i + 1) . '"/>'
                . '<wp:cNvGraphicFramePr>'
                . '<a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/>'
                . '</wp:cNvGraphicFramePr>'
                . '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">'
                . '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
                . '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">'
                . '<pic:nvPicPr>'
                . '<pic:cNvPr id="' . $uid . '" name="CrxImg' . ($i + 1) . '"/>'
                . '<pic:cNvPicPr><a:picLocks noChangeAspect="1" noChangeArrowheads="1"/></pic:cNvPicPr>'
                . '</pic:nvPicPr>'
                . '<pic:blipFill>'
                . '<a:blip r:embed="' . $rid . '"'
                . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/>'
                . '<a:stretch><a:fillRect/></a:stretch>'
                . '</pic:blipFill>'
                . '<pic:spPr bwMode="auto">'
                . '<a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $cx . '" cy="' . $cy . '"/></a:xfrm>'
                . '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>'
                . '<a:noFill/>'
                . '</pic:spPr>'
                . '</pic:pic></a:graphicData></a:graphic>'
                . '</wp:anchor>'
                . '</w:drawing></w:r></w:p>';
        }

        if ($insertXml) {
            // Insérer avant </w:body>
            $docXml = str_replace('</w:body>', $insertXml . '</w:body>', $docXml);
            $zip->addFromString('word/document.xml', $docXml);
            $zip->addFromString('word/_rels/document.xml.rels', $relsXml);
        }
    }

    /**
     * Prépare le HTML sauvegardé pour dompdf :
     * - Remplace TOUT le CSS <style> par un CSS minimal que dompdf comprend
     * - Conserve les styles inline (dompdf les supporte bien)
     * - Supprime les divs overlay (.crx-*) incompatibles avec le rendu PDF
     */
    private function cleanHtmlForDomPdf(string $html): string
    {
        $body = $this->extractHtmlBody($html);

        // Supprimer les divs overlay (watermark, textbox) — position:absolute non géré par dompdf
        $body = preg_replace('/<div[^>]*class="crx-[^"]*"[^>]*>.*?<\/div>/s', '', $body) ?? $body;

        // CSS minimal sûr pour dompdf — on ignore le CSS original (trop complexe)
        $minimalCss = '
body{font-family:"DejaVu Sans",Arial,sans-serif;font-size:11pt;color:#000;line-height:1.5;margin:15mm 20mm}
p{margin:.25em 0;padding:0}
h1{font-size:16pt;font-weight:bold;margin:.5em 0 .25em}
h2{font-size:14pt;font-weight:bold;margin:.4em 0 .2em}
h3,h4{font-size:12pt;font-weight:bold;margin:.3em 0 .15em}
table{border-collapse:collapse;width:100%;margin:.4em 0}
td,th{border:1px solid #ccc;padding:4px 8px;vertical-align:top;font-size:10pt}
th{background:#f0f0f0;font-weight:bold}
ul,ol{padding-left:1.8em;margin:.2em 0}
li{margin:.1em 0}
strong,b{font-weight:bold}
em,i{font-style:italic}
br{display:block;margin:.1em 0}
';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
             . $minimalCss
             . '</style></head><body>' . $body . '</body></html>';
    }

    // ── Prévisualisation HTML fidèle ──────────────────────────────────────────

    /**
     * Renderer priority: mammoth (Node.js) → phpoffice → LibreOffice → plain text.
     * Mammoth handles lists, images, fonts natively — no XML pre-processing needed.
     */
    public function renderHtmlPreview(string $docxPath, array $vars): string
    {
        $tempDocx = $this->applySubstitutions($docxPath, $vars);
        $renderer = env('DOCX_RENDERER', 'libreoffice');

        try {
            $loBin = $this->resolveLoBinary();

            // ── LibreOffice + Groq (pipeline prioritaire) ──────────────────────
            // LibreOffice : texte haute fidélité, tableaux, polices, alignements.
            // Groq        : positionnement précis des images ancrées (EMU → px).
            if (in_array($renderer, ['libreoffice', 'lo', 'lo+groq'], true) && $loBin) {
                return $this->convertWithLibreOfficeAndGroq($tempDocx, $loBin);
            }

            // ── phpoffice + Groq ────────────────────────────────────────────────
            if (in_array($renderer, ['phpoffice', 'php'], true)
                && class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
                $tempDocxMarked = $this->addListMarkersToDocx($tempDocx);
                if ($tempDocxMarked !== $tempDocx) { @unlink($tempDocx); $tempDocx = $tempDocxMarked; }
                return $this->convertWithPhpWord($tempDocx);
            }

            // ── mammoth ────────────────────────────────────────────────────────
            if (in_array($renderer, ['mammoth'], true)) {
                $html = $this->convertWithMammoth($tempDocx);
                if ($html !== '') return $this->injectDocxOverlays($html, $tempDocx);
            }

            // ── Fallbacks automatiques ─────────────────────────────────────────
            if ($loBin) return $this->convertWithLibreOfficeAndGroq($tempDocx, $loBin);

            if (class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
                $tempDocxMarked = $this->addListMarkersToDocx($tempDocx);
                if ($tempDocxMarked !== $tempDocx) { @unlink($tempDocx); $tempDocx = $tempDocxMarked; }
                return $this->convertWithPhpWord($tempDocx);
            }

            $html = $this->convertWithMammoth($tempDocx);
            if ($html !== '') return $this->injectDocxOverlays($html, $tempDocx);

            return $this->extractPlainTextFull($tempDocx);

        } finally {
            @unlink($tempDocx);
        }
    }

    /**
     * Convert DOCX to HTML using mammoth.js (Node.js).
     * Returns empty string on failure so the caller can fall back.
     */
    private function convertWithMammoth(string $docxPath): string
    {
        $script = base_path('tools/docx2html.js');
        if (!file_exists($script)) return '';

        $node = $this->resolveNodeBinary();
        if (!$node) return '';

        // Use double-quoted paths for Windows compatibility
        $cmd = '"' . $node . '" "' . $script . '" "' . $docxPath . '" 2>&1';

        $output   = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            // Log stderr lines (start with [mammoth]) but do not throw — fall back
            return '';
        }

        $html = implode("\n", $output);
        return (str_contains($html, '<body') && str_contains($html, '</body>')) ? $html : '';
    }

    /**
     * Inject DOCX header watermark + text-box image overlays into HTML produced
     * by mammoth (which ignores DOCX headers/footers).
     * Does NOT inject inline body images — mammoth already embeds them.
     */
    private function injectDocxOverlays(string $html, string $docxPath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) return $html;

        // Load relationship map
        $bodyRels = $zip->getFromName('word/_rels/document.xml.rels') ?: '';
        preg_match_all('/Id="([^"]+)"[^>]+Target="([^"]+)"/', $bodyRels, $rm);
        $relMap = array_combine($rm[1], $rm[2]);

        // ── Watermark from header ────────────────────────────────────────────
        $watermarkHtml = '';
        $hdrRels = $zip->getFromName('word/_rels/header1.xml.rels') ?: '';
        if (preg_match('/Target="([^"]*image[^"]*)"/', $hdrRels, $wm)) {
            $wmPath = 'word/' . ltrim($wm[1], '/');
            $wmData = $zip->getFromName($wmPath);
            if ($wmData) {
                $mime = str_ends_with(strtolower($wmPath), '.jpg') ? 'image/jpeg' : 'image/png';
                $b64  = base64_encode($wmData);
                $watermarkHtml = '<style>'
                    . '.crx-watermark{position:absolute;inset:0;z-index:0;pointer-events:none;'
                    . 'background:url("data:' . $mime . ';base64,' . $b64 . '") center/contain no-repeat;opacity:.12}'
                    . '</style>'
                    . '<div class="crx-watermark"></div>';
            }
        }

        // ── Text-box images from <mc:AlternateContent>/<mc:Fallback>/<v:shape> ─
        $docXml      = $zip->getFromName('word/document.xml') ?: '';
        $textBoxDivs = '';
        $textBoxRids = [];

        $offset = 0;
        while (($acStart = stripos($docXml, '<mc:AlternateContent', $offset)) !== false) {
            $acEnd = stripos($docXml, '</mc:AlternateContent>', $acStart);
            if ($acEnd === false) break;
            $acBlock = substr($docXml, $acStart, $acEnd - $acStart + strlen('</mc:AlternateContent>'));
            $offset  = $acEnd + 1;

            $fbStart = stripos($acBlock, '<mc:Fallback');
            if ($fbStart === false) continue;
            $fbEnd = stripos($acBlock, '</mc:Fallback>', $fbStart);
            if ($fbEnd === false) continue;
            $fbBlock = substr($acBlock, $fbStart, $fbEnd - $fbStart);

            if (!preg_match('/<v:shape\b[^>]*style="([^"]*)"/', $fbBlock, $vsm)) continue;
            $styleStr = $vsm[1];

            $marginLeft = 0.0; $marginTop = 0.0; $width = 0.0; $height = 0.0;
            if (preg_match('/margin-left\s*:\s*(-?[\d.]+)pt/i', $styleStr, $m)) $marginLeft = (float)$m[1];
            if (preg_match('/margin-top\s*:\s*(-?[\d.]+)pt/i',  $styleStr, $m)) $marginTop  = (float)$m[1];
            if (preg_match('/width\s*:\s*(-?[\d.]+)pt/i',       $styleStr, $m)) $width      = (float)$m[1];
            if (preg_match('/height\s*:\s*(-?[\d.]+)pt/i',      $styleStr, $m)) $height     = (float)$m[1];

            $leftPx   = round(($marginLeft + 72) * 96 / 72);
            $topPx    = round($marginTop  * 96 / 72);
            $widthPx  = round($width      * 96 / 72);
            $heightPx = round($height     * 96 / 72);

            if (!preg_match('/r:id="([^"]+)"/i', $acBlock, $ridm)) {
                if (!preg_match('/r:embed="([^"]+)"/i', $acBlock, $ridm)) continue;
            }
            $rid    = $ridm[1];
            $target = $relMap[$rid] ?? null;
            if (!$target) continue;
            $imgPath = 'word/' . ltrim($target, '/');
            $imgData = $zip->getFromName($imgPath);
            if (!$imgData) continue;

            $ext  = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
            $mime = match($ext) { 'jpg','jpeg' => 'image/jpeg', 'gif' => 'image/gif', default => 'image/png' };
            $src  = 'data:' . $mime . ';base64,' . base64_encode($imgData);

            $textBoxRids[] = $rid;
            $textBoxDivs  .= sprintf(
                '<div style="position:absolute;left:%dpx;top:%dpx;width:%dpx;height:%dpx;overflow:hidden">'
                . '<img src="%s" data-docx-auto="1" style="width:100%%;height:100%%;object-fit:contain">'
                . '</div>',
                max(0, $leftPx), max(0, $topPx), $widthPx, $heightPx, $src
            );
        }

        $zip->close();

        // Inject after <body>
        if ($watermarkHtml) {
            $html = preg_replace('/<body([^>]*)>/i', '<body$1>' . $watermarkHtml, $html, 1) ?? $html;
        }
        if ($textBoxDivs) {
            $tbWrap = '<div class="crx-textboxes" style="position:absolute;inset:0;pointer-events:none;z-index:10">'
                    . $textBoxDivs . '</div>';
            $html = preg_replace('/<body([^>]*)>/i', '<body$1>' . $tbWrap, $html, 1) ?? $html;
        }

        return $html;
    }

    /** Resolve the node binary path. */
    private function resolveNodeBinary(): ?string
    {
        $bin = env('NODE_BIN', 'node');
        if ($bin !== 'node' && file_exists($bin)) return $bin;

        // Windows standard location
        $win = 'C:\\Program Files\\nodejs\\node.exe';
        if (file_exists($win)) return $win;

        // Check PATH
        $test = PHP_OS_FAMILY === 'Windows' ? 'where node 2>NUL' : 'which node 2>/dev/null';
        exec($test, $out, $code);
        if ($code === 0 && !empty($out[0])) return trim($out[0]);

        return null;
    }

    private function convertWithPhpWord(string $docxPath): string
    {
        if (method_exists(\PhpOffice\PhpWord\Settings::class, 'setOutputEscapingEnabled')) {
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        }

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($docxPath);
        $writer  = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $writer->save('php://output');
        $html = ob_get_clean() ?: '';

        return $this->injectDocxImages($html, $docxPath);
    }

    /**
     * Fix E: Injects watermark as background + body images.
     * Text box images (inside <mc:AlternateContent>) are injected as position:absolute
     * divs using coordinates parsed from <v:shape style="margin-left:Xpt;margin-top:Ypt;...">.
     */
    private function injectDocxImages(string $html, string $docxPath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) return $html;

        // Load relationship map
        $bodyRels = $zip->getFromName('word/_rels/document.xml.rels') ?: '';
        preg_match_all('/Id="([^"]+)"[^>]+Target="([^"]+)"/', $bodyRels, $rm);
        $relMap = array_combine($rm[1], $rm[2]);

        // Watermark from header
        $watermarkCss = '';
        $hdrRels = $zip->getFromName('word/_rels/header1.xml.rels') ?: '';
        if (preg_match('/Target="([^"]*image[^"]*)"/', $hdrRels, $wm)) {
            $wmPath = 'word/' . ltrim($wm[1], '/');
            $wmData = $zip->getFromName($wmPath);
            if ($wmData) {
                $mime = str_ends_with(strtolower($wmPath), '.jpg') ? 'image/jpeg' : 'image/png';
                $b64  = base64_encode($wmData);
                $watermarkCss = '<style>'
                    . '.crx-watermark{position:absolute;inset:0;z-index:0;pointer-events:none;'
                    . 'background:url("data:' . $mime . ';base64,' . $b64 . '") center/contain no-repeat;'
                    . 'opacity:.12}'
                    . '</style>'
                    . '<div class="crx-watermark"></div>';
            }
        }

        $docXml = $zip->getFromName('word/document.xml') ?: '';

        // Fix E: Parse text boxes from <mc:AlternateContent> / <mc:Fallback> / <v:shape>
        // Collect text box images with absolute positioning
        $textBoxDivs = '';
        $textBoxRids = [];

        // Find all <mc:AlternateContent> blocks
        $offset = 0;
        while (($acStart = stripos($docXml, '<mc:AlternateContent', $offset)) !== false) {
            $acEnd = stripos($docXml, '</mc:AlternateContent>', $acStart);
            if ($acEnd === false) break;
            $acBlock = substr($docXml, $acStart, $acEnd - $acStart + strlen('</mc:AlternateContent>'));
            $offset  = $acEnd + 1;

            // Look for <mc:Fallback> inside the block
            $fbStart = stripos($acBlock, '<mc:Fallback');
            if ($fbStart === false) continue;
            $fbEnd = stripos($acBlock, '</mc:Fallback>', $fbStart);
            if ($fbEnd === false) continue;
            $fbBlock = substr($acBlock, $fbStart, $fbEnd - $fbStart);

            // Parse <v:shape style="..."> for position
            if (!preg_match('/<v:shape\b[^>]*style="([^"]*)"/', $fbBlock, $vsm)) continue;
            $styleStr = $vsm[1];

            // Extract position values (in pt)
            $marginLeft = 0.0;
            $marginTop  = 0.0;
            $width      = 0.0;
            $height     = 0.0;

            if (preg_match('/margin-left\s*:\s*(-?[\d.]+)pt/i', $styleStr, $m)) $marginLeft = (float)$m[1];
            if (preg_match('/margin-top\s*:\s*(-?[\d.]+)pt/i',  $styleStr, $m)) $marginTop  = (float)$m[1];
            if (preg_match('/width\s*:\s*(-?[\d.]+)pt/i',        $styleStr, $m)) $width      = (float)$m[1];
            if (preg_match('/height\s*:\s*(-?[\d.]+)pt/i',       $styleStr, $m)) $height     = (float)$m[1];

            // Convert pt → px (96dpi): 1pt = 96/72 px
            $leftPx   = round(($marginLeft + 72) * 96 / 72); // +72pt for Word left margin
            $topPx    = round($marginTop  * 96 / 72);
            $widthPx  = round($width      * 96 / 72);
            $heightPx = round($height     * 96 / 72);

            // Find image rId inside this alternate content block
            if (!preg_match('/r:id="([^"]+)"/i', $acBlock, $ridm)) {
                // Try blip embed
                if (!preg_match('/r:embed="([^"]+)"/i', $acBlock, $ridm)) continue;
            }
            $rid = $ridm[1];

            $target = $relMap[$rid] ?? null;
            if (!$target) continue;
            $imgPath = 'word/' . ltrim($target, '/');
            $imgData = $zip->getFromName($imgPath);
            if (!$imgData) continue;

            $ext  = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
            $mime = match($ext) { 'jpg','jpeg' => 'image/jpeg', 'gif' => 'image/gif', default => 'image/png' };
            $src  = 'data:' . $mime . ';base64,' . base64_encode($imgData);

            $textBoxRids[] = $rid;
            // data-docx-auto marks this as auto-injected (stripped on save, re-injected on load)
            $textBoxDivs  .= sprintf(
                '<div style="position:absolute;left:%dpx;top:%dpx;width:%dpx;height:%dpx;overflow:hidden">'
                . '<img src="%s" data-docx-auto="1" style="width:100%%;height:100%%;object-fit:contain">'
                . '</div>',
                max(0, $leftPx), max(0, $topPx), $widthPx, $heightPx, $src
            );
        }

        // ── wp:anchor images — positionnement via agent Groq ─────────────────
        // Groq analyse XML (EMU, relativeFrom, paragraphes) et retourne des
        // positions CSS pixel précises. Le zip est déjà ouvert : on génère les
        // <img> directement sans second appel I/O.
        $groqResult = $this->groqRenderer->analyzeAndPosition($docxPath);
        $anchorRids = [];

        foreach ($groqResult['images'] ?? [] as $pos) {
            $rid = $pos['rid'] ?? null;
            if (!$rid) continue;
            $target = $relMap[$rid] ?? null;
            if (!$target) continue;
            $imgPath = 'word/' . ltrim($target, '/');
            $imgData = $zip->getFromName($imgPath);
            if (!$imgData) continue;

            $ext  = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
            $mime = match($ext) { 'jpg','jpeg' => 'image/jpeg', 'gif' => 'image/gif', default => 'image/png' };
            $src  = 'data:' . $mime . ';base64,' . base64_encode($imgData);

            $left    = max(0, (int)($pos['left']    ?? 0));
            $top     = max(0, (int)($pos['top']     ?? 0));
            $width   = max(1, (int)($pos['width']   ?? 80));
            $height  = max(1, (int)($pos['height']  ?? 80));
            $zIndex  = (int)($pos['zIndex']   ?? 10);
            $opacity = number_format((float)($pos['opacity'] ?? 1.0), 2);

            $anchorRids[]  = $rid;
            $textBoxDivs  .= sprintf(
                '<img src="%s" data-docx-auto="1" '
                . 'style="position:absolute;left:%dpx;top:%dpx;width:%dpx;height:%dpx;'
                . 'z-index:%d;opacity:%s;object-fit:contain;pointer-events:none">',
                $src, $left, $top, $width, $height, $zIndex, $opacity
            );
        }

        // Inline body images (blips) — skip rIds déjà utilisés (textboxes + anchors Groq)
        preg_match_all('/<a:blip[^>]+r:embed="([^"]+)"/', $docXml, $blips);
        $uniqueRids  = array_values(array_unique($blips[1]));
        $inlineRids  = array_diff($uniqueRids, $textBoxRids, $anchorRids);

        $imagesHtml = '';
        foreach ($inlineRids as $rid) {
            $target = $relMap[$rid] ?? null;
            if (!$target) continue;
            $imgPath = 'word/' . ltrim($target, '/');
            $imgData = $zip->getFromName($imgPath);
            if (!$imgData) continue;
            $ext  = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
            $mime = match($ext) { 'jpg','jpeg' => 'image/jpeg', 'gif' => 'image/gif', default => 'image/png' };
            $src  = 'data:' . $mime . ';base64,' . base64_encode($imgData);
            $imagesHtml .= '<div style="text-align:center;margin:6px 0">'
                         . '<img src="' . $src . '" data-docx-auto="1" style="max-width:88%;max-height:140px;height:auto;object-fit:contain">'
                         . '</div>';
        }

        $zip->close();

        // Inject watermark after <body>
        if ($watermarkCss) {
            $html = preg_replace('/<body([^>]*)>/i', '<body$1>' . $watermarkCss, $html, 1);
        }

        // Inject text box container (position:absolute wrapper) after <body>
        if ($textBoxDivs) {
            $tbHtml = '<div class="crx-textboxes" style="position:absolute;inset:0;pointer-events:none;z-index:10">'
                    . $textBoxDivs . '</div>';
            $html = preg_replace('/<body([^>]*)>/i', '<body$1>' . $tbHtml, $html, 1);
        }

        // Inject inline images at top of first page
        if ($imagesHtml) {
            $html = preg_replace(
                '/(<div[^>]*page[^>]*>)\s*(<p)/i',
                '$1' . $imagesHtml . '$2',
                $html, 1
            );
        }

        return $html;
    }

    /**
     * Re-injects DOCX images into previously-saved HTML (for edit reload).
     */
    public function reInjectImages(string $html, string $docxPath): string
    {
        return $this->injectDocxImages($html, $docxPath);
    }

    /**
     * Pipeline combiné LibreOffice + Groq :
     *  1. LibreOffice  → HTML texte haute fidélité (polices, tableaux, listes, alignements)
     *  2. Strip images LO (sd-abs-pos) — elles sont mal référencées dans leur système de coordonnées
     *  3. Groq         → positions pixel précises pour chaque image ancrée (depuis XML DOCX)
     *  4. DOCX zip     → données image base64 (même source de vérité que les positions Groq)
     *  5. Injection    → watermark CSS + images Groq positionnées en overlay absolu
     */
    private function convertWithLibreOfficeAndGroq(string $docxPath, ?string $binary = null): string
    {
        $outDir = storage_path('app/temp');
        if (!is_dir($outDir)) mkdir($outDir, 0755, true);

        $binary ??= $this->resolveLoBinary() ?? 'soffice';
        $stem    = pathinfo($docxPath, PATHINFO_FILENAME);
        $cmd     = sprintf('"%s" --headless --convert-to html --outdir "%s" "%s" 2>&1',
                           $binary, $outDir, $docxPath);
        exec($cmd, $out, $code);

        $htmlFile = $outDir . '/' . $stem . '.html';

        // Nettoyage des fichiers images extraits par LO (on utilisera le zip DOCX)
        foreach (glob($outDir . '/' . $stem . '_html_*') ?: [] as $f) {
            @unlink($f);
        }

        if ($code !== 0 || !file_exists($htmlFile)) {
            \Illuminate\Support\Facades\Log::warning('[LO+Groq] LibreOffice a échoué (code ' . $code . '), fallback phpoffice.');
            return $this->convertWithPhpWord($docxPath);
        }

        $html = file_get_contents($htmlFile) ?: '';
        @unlink($htmlFile);

        if (trim($html) === '') {
            return $this->convertWithPhpWord($docxPath);
        }

        // ── Nettoyer les images positionnées de LO ────────────────────────────
        // LO utilise des coords cm relatives à des ancres de paragraphe — ambiguïtes.
        // On supprime les <span sd-abs-pos> et les <img> résiduels pour ré-injecter
        // avec les coords pixel exactes de Groq (depuis l'XML DOCX).
        $html = preg_replace('/<span\b[^>]*class="sd-abs-pos"[^>]*>.*?<\/span>/is', '', $html) ?? $html;

        // Supprimer les <img> restants qui référencent des fichiers _html_* maintenant supprimés
        $html = preg_replace('/<img\b[^>]*src="[^"]*_html_[^"]*"[^>]*\/?>/i', '', $html) ?? $html;

        // ── Watermark (header DOCX) + images Groq ────────────────────────────
        // injectDocxImages() gère : watermark header, images Groq, textboxes mc:AC
        $html = $this->injectDocxImages($html, $docxPath);

        return $html;
    }

    private function convertWithLibreOffice(string $docxPath, ?string $binary = null): string
    {
        $outDir = storage_path('app/temp');
        if (!is_dir($outDir)) mkdir($outDir, 0755, true);

        $binary ??= $this->resolveLoBinary() ?? env('LIBREOFFICE_BIN', 'soffice');
        $cmd    = sprintf('"%s" --headless --convert-to html --outdir "%s" "%s" 2>&1',
                          $binary, $outDir, $docxPath);
        exec($cmd, $out, $code);

        $htmlFile = $outDir . '/' . pathinfo($docxPath, PATHINFO_FILENAME) . '.html';
        if ($code !== 0 || !file_exists($htmlFile)) {
            return '<html><body><p style="color:red">Conversion LibreOffice échouée (code ' . $code . ').</p></body></html>';
        }

        $html = file_get_contents($htmlFile) ?: '';
        @unlink($htmlFile);

        // LibreOffice extracts images as separate files alongside the HTML.
        // Embed them as base64 so the HTML is self-contained.
        $html = (string) preg_replace_callback(
            '/<img\b([^>]*)src="([^"#][^"]*)"([^>]*)>/i',
            static function ($m) use ($outDir) {
                $src = $m[2];
                if (str_starts_with($src, 'data:') || preg_match('#^https?://#i', $src)) return $m[0];
                $imgPath = $outDir . '/' . ltrim($src, '/\\');
                if (!file_exists($imgPath)) return $m[0];
                $ext  = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
                $mime = match ($ext) { 'jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', default => 'image/png' };
                $b64  = base64_encode(file_get_contents($imgPath));
                @unlink($imgPath);
                return '<img' . $m[1] . 'src="data:' . $mime . ';base64,' . $b64 . '" data-docx-auto="1"' . $m[3] . '>';
            },
            $html
        );

        return $html ?: '<html><body><p>Fichier HTML vide.</p></body></html>';
    }

    private function extractPlainTextFull(string $docxPath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return '<html><body><p>Impossible de lire le fichier DOCX.</p></body></html>';
        }
        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        $xml  = $this->mergeAdjacentRuns($xml);
        $xml  = str_replace(['</w:p>', '</w:tr>', '<w:tab/>'], ["\n", "\n", "\t"], $xml);
        $text = html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = preg_replace("/\n{3,}/", "\n\n", trim($text)) ?? $text;

        $body = '';
        foreach (explode("\n", $text) as $line) {
            $line = rtrim($line);
            if ($line === '') { $body .= '<p>&nbsp;</p>'; continue; }
            $up   = mb_strtoupper(trim($line)) === trim($line) && mb_strlen(trim($line)) > 3;
            $s    = $up ? 'font-weight:700;color:#0B1A2E;font-size:11px' : 'font-size:11px;color:#1a1a2e';
            $body .= '<p style="' . $s . ';margin:.3em 0;line-height:1.7">' . htmlspecialchars($line) . '</p>';
        }

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
             . 'body{font-family:DejaVu Sans,Arial,sans-serif;padding:30px 40px;line-height:1.7}</style>'
             . '</head><body>' . $body . '</body></html>';
    }

    // ── Fix D: List marker injection ─────────────────────────────────────────

    /**
     * Creates a temp DOCX copy with list markers injected as <w:r> runs,
     * so phpoffice/phpword renders them as visible bullet/number text.
     * Returns the new path (caller must unlink when done).
     */
    private function addListMarkersToDocx(string $docxPath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) return $docxPath;

        $numXml = $zip->getFromName('word/numbering.xml') ?: '';
        $docXml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        if (!$numXml || !$docXml || !str_contains($docXml, '<w:numPr>')) return $docxPath;

        $modifiedXml = $this->injectListMarkersIntoXml($docXml, $numXml);

        $newPath = sys_get_temp_dir() . '/' . uniqid('docx_m_') . '.docx';
        copy($docxPath, $newPath);

        $zip2 = new ZipArchive();
        if ($zip2->open($newPath) !== true) return $docxPath;
        $zip2->addFromString('word/document.xml', $modifiedXml);
        $zip2->close();

        return $newPath;
    }

    private function injectListMarkersIntoXml(string $docXml, string $numXml): string
    {
        $numDefs  = $this->parseNumberingDefs($numXml);
        $counters = [];

        return (string) preg_replace_callback(
            '/<w:p\b[^>]*>.*?<\/w:p>/s',
            function ($m) use ($numDefs, &$counters) {
                $pXml = $m[0];
                if (!str_contains($pXml, '<w:numPr>')) return $pXml;

                preg_match('/<w:numId w:val="(\d+)"/', $pXml, $nm);
                preg_match('/<w:ilvl w:val="(\d+)"/', $pXml, $im);
                $numId  = (int)($nm[1] ?? 1);
                $ilvl   = (int)($im[1] ?? 0);
                $marker = $this->getListMarker($numDefs, $numId, $ilvl, $counters);

                // Inject indentation so phpoffice renders list paragraphs with proper indent
                // 720 twips = 0.5 inch base + 360 per additional level; 360 hanging = marker width
                $leftTwips    = 720 + ($ilvl * 360);
                $indXml       = sprintf('<w:ind w:left="%d" w:hanging="360"/>', $leftTwips);

                if (str_contains($pXml, '</w:pPr>')) {
                    // Remove any existing w:ind so ours wins
                    $pXml = preg_replace('/<w:ind\b[^\/]*\/>/', '', $pXml) ?? $pXml;
                    $pXml = str_replace('</w:pPr>', $indXml . '</w:pPr>', $pXml);
                }

                $markerRun = '<w:r><w:t xml:space="preserve">'
                           . htmlspecialchars($marker, ENT_XML1)
                           . '</w:t></w:r>';

                if (str_contains($pXml, '</w:pPr>')) {
                    return str_replace('</w:pPr>', '</w:pPr>' . $markerRun, $pXml);
                }
                return preg_replace('/(<w:p\b[^>]*>)/', '$1' . $markerRun, $pXml, 1) ?: $pXml;
            },
            $docXml
        ) ?: $docXml;
    }

    private function parseNumberingDefs(string $numXml): array
    {
        $abstracts = [];
        preg_match_all('/<w:abstractNum w:abstractNumId="(\d+)">(.*?)<\/w:abstractNum>/s', $numXml, $am);
        foreach ($am[1] as $i => $aid) {
            $levels = [];
            preg_match_all('/<w:lvl w:ilvl="(\d+)">(.*?)<\/w:lvl>/s', $am[2][$i], $lm);
            foreach ($lm[1] as $j => $lvl) {
                preg_match('/<w:numFmt w:val="([^"]+)"/', $lm[2][$j], $fmtm);
                preg_match('/<w:lvlText w:val="([^"]*)"/', $lm[2][$j], $txtm);
                $levels[(int)$lvl] = ['fmt' => $fmtm[1] ?? 'bullet', 'char' => $txtm[1] ?? '•'];
            }
            $abstracts[(int)$aid] = $levels;
        }

        $numMap = [];
        preg_match_all('/<w:num w:numId="(\d+)">\s*<w:abstractNumId w:val="(\d+)"/', $numXml, $nm2);
        foreach ($nm2[1] as $k => $nid) {
            $numMap[(int)$nid] = (int)$nm2[2][$k];
        }

        $result = [];
        foreach ($numMap as $numId => $absId) {
            $result[$numId] = $abstracts[$absId] ?? [];
        }
        return $result;
    }

    private function getListMarker(array $numDefs, int $numId, int $ilvl, array &$counters): string
    {
        $def  = $numDefs[$numId][$ilvl] ?? ['fmt' => 'bullet', 'char' => '-'];
        $fmt  = $def['fmt'];
        $char = $def['char'] ?? '';

        if ($fmt === 'bullet') {
            $printable = preg_replace('/[^\x20-\x7E\xC0-\xFF]/u', '', $char);
            return (($printable !== '') ? $printable : '•') . ' ';
        }

        $key = $numId . '_' . $ilvl;
        $counters[$key] = ($counters[$key] ?? 0) + 1;
        $n = $counters[$key];

        return match($fmt) {
            'lowerLetter' => chr(96 + $n) . '. ',
            'upperLetter' => chr(64 + $n) . '. ',
            'lowerRoman'  => strtolower($this->int2Roman($n)) . '. ',
            'upperRoman'  => $this->int2Roman($n) . '. ',
            'none'        => '',
            default       => $n . '. ',
        };
    }

    private function int2Roman(int $n): string
    {
        $vals = [1000=>'M',900=>'CM',500=>'D',400=>'CD',100=>'C',90=>'XC',
                 50=>'L',40=>'XL',10=>'X',9=>'IX',5=>'V',4=>'IV',1=>'I'];
        $r = '';
        foreach ($vals as $v => $s) {
            while ($n >= $v) { $r .= $s; $n -= $v; }
        }
        return $r;
    }

    // ── Rendu HTML paginé (pages A4 scoped) ──────────────────────────────────

    public function renderHtmlScoped(string $docxPath, array $vars, string $targetLang = 'fr'): array
    {
        $html = $this->renderHtmlPreview($docxPath, $vars);

        if ($targetLang !== 'fr') {
            $html = $this->translateHtml($html, $targetLang);
        }

        return $this->parseHtmlToScopedPages($html, $docxPath);
    }

    /**
     * Parse previously-saved full HTML into scoped pages.
     * Pass $docxPath to inject the detected default font.
     */
    public function parseSavedHtmlToPages(string $html, string $targetLang = 'fr', ?string $docxPath = null): array
    {
        if ($targetLang !== 'fr') {
            $html = $this->translateHtml($html, $targetLang);
        }

        $styles = $this->extractHtmlStyles($html);
        $body   = $this->extractHtmlBody($html);
        $pages  = $this->splitIntoPages($body);

        // Append detected document font LAST so it wins over any other rule
        if ($docxPath && ($font = $this->detectDefaultFont($docxPath))) {
            $styles .= "\n.docx-content { font-family: \"" . addslashes($font) . "\", \"DejaVu Sans\", Arial, sans-serif; }";
        }

        return ['styles' => $styles, 'pages' => $pages, 'pageCount' => count($pages)];
    }

    /**
     * Detect the default document font from word/styles.xml and theme fonts.
     * Returns the font name (e.g. "Agency FB") or null if not found.
     */
    private function detectDefaultFont(string $docxPath): ?string
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) return null;

        $stylesXml = $zip->getFromName('word/styles.xml') ?: '';
        $themeXml  = $zip->getFromName('word/theme/theme1.xml') ?: '';
        $zip->close();

        // Resolve theme font tokens (+mj-lt / +mn-lt) to real names
        $themeFonts = [];
        if ($themeXml) {
            if (preg_match('/<a:majorFont\b[^>]*>.*?<a:latin[^>]+typeface="([^"]+)"/s', $themeXml, $m)) {
                $themeFonts['+mj-lt'] = $m[1];
            }
            if (preg_match('/<a:minorFont\b[^>]*>.*?<a:latin[^>]+typeface="([^"]+)"/s', $themeXml, $m)) {
                $themeFonts['+mn-lt'] = $m[1];
            }
        }

        $resolve = static function (string $name) use ($themeFonts): string {
            return $themeFonts[$name] ?? $name;
        };

        // 1. docDefaults → rPrDefault → rFonts (highest priority)
        if (preg_match('/<w:docDefaults\b.*?<w:rPrDefault\b.*?<w:rFonts([^>]+)>/s', $stylesXml, $m)) {
            if (preg_match('/w:ascii="([^"]+)"/', $m[1], $f))      return $resolve($f[1]);
            if (preg_match('/w:asciiTheme="([^"]+)"/', $m[1], $f)) return $resolve($f[1]);
            if (preg_match('/w:hAnsi="([^"]+)"/', $m[1], $f))      return $resolve($f[1]);
        }

        // 2. Normal paragraph style → rPr → rFonts
        if (preg_match('/<w:style\b[^>]*w:styleId="Normal"[^>]*>.*?<\/w:style>/s', $stylesXml, $ns)) {
            if (preg_match('/w:ascii="([^"]+)"/', $ns[0], $f))      return $resolve($f[1]);
            if (preg_match('/w:asciiTheme="([^"]+)"/', $ns[0], $f)) return $resolve($f[1]);
            if (preg_match('/w:hAnsi="([^"]+)"/', $ns[0], $f))      return $resolve($f[1]);
        }

        return null;
    }

    /** Fix B: strpos-based <style> block extractor — safe on 5+ MB HTML. */
    private function extractHtmlStyles(string $html): string
    {
        $styles = [];
        $offset = 0;
        while (($s = stripos($html, '<style', $offset)) !== false) {
            $tagEnd = strpos($html, '>', $s);
            if ($tagEnd === false) break;
            $e = stripos($html, '</style>', $tagEnd + 1);
            if ($e === false) break;
            $styles[] = substr($html, $tagEnd + 1, $e - $tagEnd - 1);
            $offset   = $e + 8;
        }
        return implode("\n", $styles);
    }

    /** Fix B: strpos-based <body> content extractor — safe on 5+ MB HTML. */
    private function extractHtmlBody(string $html): string
    {
        $s = stripos($html, '<body');
        if ($s === false) return $html;
        $s = strpos($html, '>', $s);
        if ($s === false) return $html;
        $e = strripos($html, '</body>');
        if ($e === false) return substr($html, $s + 1);
        return substr($html, $s + 1, $e - $s - 1);
    }

    /** Fix B: strpos-based page splitter — handles CSS3 (page-break-*) and CSS4 (break-before/after:page). */
    public function splitIntoPages(string $html): array
    {
        $pages  = [];
        $offset = 0;

        while (true) {
            // Find earliest page-break marker: CSS3 or CSS4 (LibreOffice uses break-before:page)
            $p1 = stripos($html, 'page-break-', $offset);
            $p2 = stripos($html, 'break-before:page', $offset);
            $p3 = stripos($html, 'break-after:page', $offset);

            $pbPos = PHP_INT_MAX;
            if ($p1 !== false) $pbPos = min($pbPos, $p1);
            if ($p2 !== false) $pbPos = min($pbPos, $p2);
            if ($p3 !== false) $pbPos = min($pbPos, $p3);
            if ($pbPos === PHP_INT_MAX) break;

            // Walk back to the opening < of the element containing the break
            $elemStart = $pbPos;
            while ($elemStart > $offset && $html[$elemStart] !== '<') {
                $elemStart--;
            }
            // Walk forward to the closing >
            $elemEnd = strpos($html, '>', $pbPos);
            if ($elemEnd === false) break;

            $chunk = trim(substr($html, $offset, $elemStart - $offset));
            if (strip_tags($chunk) !== '') {
                $pages[] = $chunk;
            }
            $offset = $elemEnd + 1;
        }

        $last = trim(substr($html, $offset));
        if (strip_tags($last) !== '') {
            $pages[] = $last;
        }

        return $pages ?: [$html];
    }

    /** Fix B+C: parseHtmlToScopedPages — uses strpos helpers, fixed scopeStyles, and injects detected font. */
    private function parseHtmlToScopedPages(string $html, ?string $docxPath = null): array
    {
        $rawStyles    = $this->extractHtmlStyles($html);
        $scopedStyles = $this->scopeStyles($rawStyles, '.docx-content');
        $body         = $this->extractHtmlBody($html);
        $pages        = $this->splitIntoPages($body);

        // Append the detected font LAST so it wins over any hardcoded fallback in the HTML
        if ($docxPath && ($font = $this->detectDefaultFont($docxPath))) {
            $scopedStyles .= "\n.docx-content { font-family: \"" . addslashes($font) . "\", \"DejaVu Sans\", Arial, sans-serif; }";
        }

        return ['styles' => $scopedStyles, 'pages' => $pages, 'pageCount' => count($pages)];
    }

    /**
     * Fix C: Scope CSS rules to $scope.
     * body/html selectors map directly to $scope (not "$scope body") so that
     * phpoffice font-family, font-size and color rules take effect.
     */
    private function scopeStyles(string $css, string $scope): string
    {
        // Strip comments first (PCRE on just the CSS string is safe — it's tiny)
        $css = preg_replace('/\/\*[\s\S]*?\*\//', '', $css) ?? $css;

        return (string) preg_replace_callback(
            '/([^@{}\r\n][^{}]*?)\s*\{([^{}]*)\}/s',
            static function ($m) use ($scope) {
                $selector = trim($m[1]);
                if ($selector === '') return '';

                $parts = array_map(
                    static function ($s) use ($scope) {
                        $s = trim($s);
                        // crx-* overlays are moved to page level by JS — never scope them
                        if (preg_match('/^\.crx-/i', $s)) return $s;
                        // body/html alone → the scope wrapper itself
                        if (in_array($s, ['body', 'html'], true)) return $scope;
                        // "body *" / "html *" → scope children
                        if (in_array($s, ['body *', 'html *'], true)) return $scope . ' *';
                        // "body .foo" / "html .foo" → strip leading body/html
                        if (preg_match('/^(?:body|html)\s+(.+)$/i', $s, $mm)) {
                            return $scope . ' ' . $mm[1];
                        }
                        return $scope . ' ' . $s;
                    },
                    explode(',', $selector)
                );

                return implode(', ', $parts) . ' {' . $m[2] . '}';
            },
            $css
        );
    }

    // ── Traduction automatique HTML ───────────────────────────────────────────

    public function translateHtml(string $html, string $targetLang): string
    {
        if ($targetLang === 'fr') return $html;

        $cacheKey = 'docx_tr_v2_' . md5($html) . '_' . $targetLang;
        return cache()->remember($cacheKey, 86400, fn () => $this->doTranslateHtml($html, $targetLang));
    }

    private function doTranslateHtml(string $html, string $targetLang): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $textNodes = $xpath->query(
            '//body//text()[normalize-space() != ""'
            . ' and not(ancestor::script)'
            . ' and not(ancestor::style)]'
        );

        $nodeList = [];
        $textList = [];

        foreach ($textNodes as $node) {
            $text = $node->nodeValue;
            if (preg_match('/[a-zA-ZÀ-ÿ]{2,}/', $text)) {
                $nodeList[] = $node;
                $textList[] = $text;
            }
        }

        if (empty($textList)) return $html;

        $translated = $this->batchTranslate($textList, 'fr', $targetLang);

        foreach ($nodeList as $i => $node) {
            if (!empty($translated[$i])) {
                $node->nodeValue = $translated[$i];
            }
        }

        return $dom->saveHTML() ?: $html;
    }

    private function batchTranslate(array $texts, string $from, string $to): array
    {
        $SEP     = "\n<|||>\n";
        $results = array_fill(0, count($texts), null);

        $batches        = [];
        $batchIndices   = [];
        $current        = '';
        $currentIndices = [];

        foreach ($texts as $i => $text) {
            $addition = ($current === '' ? '' : $SEP) . $text;
            if (mb_strlen($current . $addition) > 3000 && $current !== '') {
                $batches[]        = $current;
                $batchIndices[]   = $currentIndices;
                $current          = $text;
                $currentIndices   = [$i];
            } else {
                $current          .= $addition;
                $currentIndices[] = $i;
            }
        }
        if ($current !== '') {
            $batches[]      = $current;
            $batchIndices[] = $currentIndices;
        }

        foreach ($batches as $bi => $batch) {
            $raw = $this->callTranslateApi($batch, $from, $to);
            if ($raw === null) continue;

            $parts = preg_split('/<\|\|\|>/', $raw);
            foreach ($batchIndices[$bi] as $pi => $textIdx) {
                if (isset($parts[$pi])) {
                    $results[$textIdx] = trim($parts[$pi]);
                }
            }
        }

        return $results;
    }

    private function callTranslateApi(string $text, string $from, string $to): ?string
    {
        $url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query([
            'client' => 'gtx',
            'sl'     => $from,
            'tl'     => $to,
            'dt'     => 't',
            'q'      => $text,
        ]);

        $ctx = stream_context_create(['http' => [
            'method'  => 'GET',
            'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
            'timeout' => 12,
        ]]);

        $response = @file_get_contents($url, false, $ctx);
        if (!$response) return null;

        $data = json_decode($response, true);
        if (!is_array($data) || !isset($data[0])) return null;

        $result = '';
        foreach ((array) $data[0] as $segment) {
            if (is_array($segment) && isset($segment[0])) {
                $result .= $segment[0];
            }
        }

        return $result ?: null;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function commandExists(string $command): bool
    {
        $test = PHP_OS_FAMILY === 'Windows' ? "where $command" : "which $command";
        exec($test, $out, $code);
        return $code === 0;
    }

    /**
     * Resolve the LibreOffice binary path.
     * Priority: LIBREOFFICE_BIN env → standard Windows/Linux paths → PATH.
     */
    private function resolveLoBinary(): ?string
    {
        $envBin = env('LIBREOFFICE_BIN', 'soffice');

        // 1. Value in .env points to an actual file
        if ($envBin !== 'soffice' && file_exists($envBin)) return $envBin;

        // 2. Standard Windows installation paths
        $standardPaths = [
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
        ];
        // Also try versioned paths (e.g. LibreOffice 24.x)
        foreach (glob('C:\\Program Files\\LibreOffice*\\program\\soffice.exe') ?: [] as $p) {
            $standardPaths[] = $p;
        }
        // Linux / macOS
        $standardPaths[] = '/usr/bin/soffice';
        $standardPaths[] = '/usr/lib/libreoffice/program/soffice';
        $standardPaths[] = '/Applications/LibreOffice.app/Contents/MacOS/soffice';

        foreach ($standardPaths as $path) {
            if (file_exists($path)) return $path;
        }

        // 3. Fall back to PATH lookup
        if ($this->commandExists('soffice')) return 'soffice';

        return null;
    }
}
