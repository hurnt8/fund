<?php

namespace App\Services;

use App\Models\ContractTemplate;
use App\Models\LoanRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ContractHtmlService
{
    public function __construct(
        private ContractService $contractService
    ) {}

    /**
     * Génère le PDF du contrat et retourne son chemin absolu.
     */
    public function generate(LoanRequest $loan, ContractTemplate $template = null): string
    {
        $tpl    = $template ?? $loan->contractTemplate;
        $locale = $loan->contract_language ?? 'fr';
        $html   = $this->buildContractHtml($loan, $tpl, $locale);
        return $this->renderPdf($html, $loan->reference . '_' . $locale);
    }

    /**
     * Génère un HTML d'aperçu et retourne son chemin de fichier PDF absolu.
     */
    public function generatePreview(ContractTemplate $template, array $extraVars = [], string $locale = 'fr'): string
    {
        $html = $this->buildPreviewHtml($template, $extraVars, $locale);
        return $this->renderPdf($html, 'preview_' . $template->id . '_' . uniqid());
    }

    /**
     * Retourne le HTML complet du contrat (sans générer de PDF).
     */
    public function buildContractHtml(LoanRequest $loan, ?ContractTemplate $template, string $locale = 'fr'): string
    {
        $vars   = $this->contractService->getVariables($loan);
        $t      = $this->contractService->getTranslations($locale);
        $header = $this->contractService->getCountryHeader($locale);
        $images = $this->loadImagesBase64($template);

        if ($template && $template->content) {
            return $this->renderCustomContent($template, $t, $vars, $header, $images);
        }

        return view('contracts.template', [
            't'      => $t,
            'vars'   => $vars,
            'header' => $header,
            'loan'   => $loan,
            'tpl'    => $template,
            'images' => $images,
        ])->render();
    }

    /**
     * Retourne le HTML d'aperçu avec des données de démonstration.
     */
    public function buildPreviewHtml(ContractTemplate $template, array $extraVars, string $locale): string
    {
        $translVars = $this->contractService->getTranslationVars($locale);
        $t          = $this->contractService->getTranslations($locale);
        $header     = $this->contractService->getCountryHeader($locale);
        $images     = $this->loadImagesBase64($template);
        $sampleVars = $this->getSampleVars($locale);
        $vars       = array_merge($sampleVars, $extraVars);

        if ($template->content) {
            return $this->renderCustomContent($template, $t, $vars, $header, $images);
        }

        return view('contracts.template', [
            't'      => $t,
            'vars'   => $vars,
            'header' => $header,
            'loan'   => null,
            'tpl'    => $template,
            'images' => $images,
        ])->render();
    }

    /**
     * Rend un template HTML personnalisé (contenu BDD) avec toutes les balises substituées.
     */
    private function renderCustomContent(
        ContractTemplate $template,
        array $t,
        array $vars,
        string $header,
        array $images
    ): string {
        $translationVars = ['{header}' => $header];
        foreach ($t as $key => $value) {
            $translationVars['{' . $key . '}'] = $value;
        }

        $all     = array_merge($translationVars, $vars);
        $content = $template->content;
        $content = str_replace(array_keys($all), array_values($all), $content);
        $content = str_replace(array_keys($all), array_values($all), $content);

        $watermarkHtml = $this->buildWatermarkHtml($images['watermark_path'] ?? null);
        $logosHtml     = $this->buildLogosHtml(
            $images['logo_left_path']  ?? null,
            $images['logo_right_path'] ?? null
        );
        $sigsHtml = $this->buildSignaturesHtml(
            $images['signature_admin_path'] ?? null,
            $images['stamp_path']           ?? null,
            $images['signature_agent_path'] ?? null
        );

        return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><style>'
            . $this->buildCss()
            . '</style></head><body>'
            . $watermarkHtml
            . '<div class="page">'
            . $logosHtml
            . $content
            . $sigsHtml
            . '</div></body></html>';
    }

    /**
     * Charge les images du template en data URIs base64 (DomPDF ne peut pas accéder aux URLs HTTP).
     */
    public function loadImagesBase64(?ContractTemplate $template): array
    {
        $fields = [
            'watermark_path',
            'logo_left_path',
            'logo_right_path',
            'stamp_path',
            'signature_admin_path',
            'signature_agent_path',
        ];

        $images = array_fill_keys($fields, null);
        if (!$template) {
            return $images;
        }

        foreach ($fields as $field) {
            $relPath = $template->$field ?? null;
            if (!$relPath) {
                continue;
            }
            $absPath = Storage::disk('public')->path($relPath);
            if (!file_exists($absPath)) {
                continue;
            }
            $mime           = mime_content_type($absPath) ?: 'image/png';
            $images[$field] = 'data:' . $mime . ';base64,' . base64_encode(
                file_get_contents($absPath)
            );
        }

        return $images;
    }

    private function buildWatermarkHtml(?string $base64): string
    {
        if ($base64) {
            return '<div class="crx-wm" style="background-image:url(\'' . $base64 . '\')"></div>';
        }
        return '<div class="crx-wm-text">CONFIDENTIEL</div>';
    }

    private function buildLogosHtml(?string $leftB64, ?string $rightB64): string
    {
        if (!$leftB64 && !$rightB64) {
            return '';
        }
        $left  = $leftB64  ? '<img src="' . $leftB64  . '" style="max-height:55px;max-width:150px;object-fit:contain">' : '<span></span>';
        $right = $rightB64 ? '<img src="' . $rightB64 . '" style="max-height:55px;max-width:150px;object-fit:contain">' : '<span></span>';

        return '<div style="display:table;width:100%;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #ccc;">'
            . '<div style="display:table-cell;text-align:left;vertical-align:middle;">'  . $left  . '</div>'
            . '<div style="display:table-cell;text-align:right;vertical-align:middle;">' . $right . '</div>'
            . '</div>';
    }

    private function buildSignaturesHtml(?string $sigAdminB64, ?string $stampB64, ?string $sigAgentB64): string
    {
        if (!$sigAdminB64 && !$stampB64 && !$sigAgentB64) {
            return '';
        }

        $html = '<div style="display:table;width:100%;margin-top:20px;">';
        foreach ([
            [$sigAdminB64, '55px'],
            [$stampB64,    '65px'],
            [$sigAgentB64, '55px'],
        ] as [$b64, $maxH]) {
            $html .= '<div style="display:table-cell;text-align:center;vertical-align:bottom;">';
            if ($b64) {
                $html .= '<img src="' . $b64 . '" style="max-height:' . $maxH . ';max-width:140px;object-fit:contain;">';
            }
            $html .= '</div>';
        }
        return $html . '</div>';
    }

    /**
     * CSS A4 professionnel — Times New Roman via DejaVu Serif (inclus dans DomPDF).
     */
    public function buildCss(): string
    {
        return '
@page {
    margin: 20mm 22mm 20mm 22mm;
}
body {
    font-family: "DejaVu Serif", "Times New Roman", Times, Georgia, serif;
    font-size: 11pt;
    color: #000000;
    line-height: 1.7;
    margin: 0;
    padding: 0;
}
.page {
    padding: 0;
}
.crx-wm {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: -1; opacity: 0.06;
    background-size: 42%; background-position: center; background-repeat: no-repeat;
}
.crx-wm-text {
    position: fixed;
    top: 42%; left: 0; width: 100%;
    text-align: center;
    font-size: 60pt; font-weight: bold;
    color: rgba(0,0,0,0.04);
    pointer-events: none; z-index: -1;
    font-family: "DejaVu Serif", serif;
    letter-spacing: 10px;
}
.header {
    text-align: center;
    border-bottom: 2.5pt solid #000;
    padding-bottom: 12pt;
    margin-bottom: 16pt;
}
.header-country {
    font-size: 9pt; color: #444;
    white-space: pre-line; margin-bottom: 8pt; font-style: italic;
}
.header-title {
    font-size: 15pt; font-weight: bold; color: #000;
    letter-spacing: 1.5px; margin-bottom: 5pt; text-transform: uppercase;
}
.header-ref {
    font-size: 9pt; color: #333;
}
.section-title {
    font-size: 10pt; font-weight: bold; color: #000;
    text-transform: uppercase; letter-spacing: 1px;
    border-bottom: 1pt solid #888;
    margin: 16pt 0 8pt; padding-bottom: 3pt;
}
.parties-block {
    margin-bottom: 9pt; font-size: 10.5pt;
}
.party-label {
    font-weight: bold;
}
.finance-table {
    width: 100%; border-collapse: collapse;
    margin: 8pt 0 14pt; font-size: 10.5pt;
}
.finance-table td {
    padding: 5pt 9pt; border: 0.5pt solid #999;
}
.finance-table td:first-child {
    background: #f5f5f5; font-weight: bold; width: 55%;
}
.finance-table td:last-child {
    font-weight: bold; font-size: 11.5pt;
}
.article {
    margin-bottom: 12pt;
}
.article-title {
    font-weight: bold; font-size: 10.5pt;
    margin-bottom: 4pt; text-decoration: underline;
}
.article-body {
    font-size: 10.5pt; color: #111;
    line-height: 1.75; white-space: pre-line;
}
.signature-block {
    margin-top: 24pt;
    border-top: 1.5pt solid #000;
    padding-top: 12pt;
}
.sig-date {
    margin-bottom: 18pt; font-size: 10.5pt;
}
.sig-row {
    display: table; width: 100%;
}
.sig-cell {
    display: table-cell; width: 33.33%;
    text-align: center; padding: 8pt 4pt; vertical-align: bottom;
}
.sig-label {
    font-weight: bold; font-size: 8.5pt;
    text-transform: uppercase;
    border-top: 0.5pt solid #666;
    padding-top: 4pt; margin-top: 38pt;
}
.company-name {
    font-size: 12pt; font-weight: bold; letter-spacing: 2px;
}
';
    }

    /**
     * Variables de démonstration pour les aperçus.
     */
    private function getSampleVars(string $locale): array
    {
        return [
            '{reference}'       => 'REF-2026-0001',
            '{archive}'         => 'ARC-2026-0001',
            '{nom_client}'      => 'Jean DUPONT',
            '{adresse_client}'  => '12 rue de la Paix, 75001 Paris',
            '{date_naissance}'  => '15/06/1985',
            '{numero_identite}' => 'AB123456',
            '{type_identite}'   => 'Passeport',
            '{agent_suivi}'     => 'Marie MARTIN',
            '{montant}'         => '15 000,00',
            '{devise}'          => 'EUR',
            '{duree}'           => '36',
            '{mensualite}'      => '450,00',
            '{taux}'            => '5,50',
            '{frais_admin}'     => '250,00',
            '{compte_bancaire}' => 'FR76 1234 5678 9012 3456 7890 123',
            '{date}'            => now()->format('d/m/Y'),
            '{societe}'         => 'AURENZA CAPITAL',
            '{ne_e}'            => 'né',
            '{nee}'             => 'né',
            '{denomme_e}'       => 'dénommé',
            '{zamieszkal_a}'    => 'zamieszkały',
            '{e}'               => '',
        ];
    }

    /**
     * Génère le PDF via DomPDF et retourne le chemin absolu du fichier.
     */
    private function renderPdf(string $html, string $suffix): string
    {
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'isPhpEnabled'         => false,
                'defaultFont'          => 'DejaVu Serif',
                'dpi'                  => 150,
                'fontHeightRatio'      => 1.1,
            ]);

        $path = 'contracts/contract_' . $suffix . '.pdf';
        Storage::disk('local')->makeDirectory('contracts');
        Storage::disk('local')->put($path, $pdf->output());

        $absPath = Storage::disk('local')->path($path);
        if (!file_exists($absPath)) {
            throw new \RuntimeException("Échec de la génération PDF : $absPath");
        }

        return $absPath;
    }
}
