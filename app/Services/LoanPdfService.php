<?php

namespace App\Services;

use App\Models\LoanRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LoanPdfService
{
    public function __construct(
        private ContractService     $contractService,
        private ContractDocxService $docxService,
    ) {}

    /**
     * Génère le PDF du contrat et retourne son chemin absolu.
     *
     * Pipeline DOCX  : template_type='docx' → substitution balises → LibreOffice (ou phpoffice+dompdf) → PDF
     * Pipeline HTML  : template_type='html' (ou défaut) → ContractService → DomPDF → PDF
     */
    public function generate(LoanRequest $loan, string $locale = 'fr'): string
    {
        $loan->contract_language = $locale;

        $template = $loan->contractTemplate;

        // ── Voie DOCX ────────────────────────────────────────────────────────
        // generatePdf() essaie LibreOffice en premier, puis phpoffice+dompdf en fallback.
        // Si le template a été modifié via l'éditeur de prévisualisation, le filigrane
        // personnalisé est injecté dans le rendu phpoffice.
        if ($template && $template->template_type === 'docx' && $template->docx_path) {
            return $this->docxService->generatePdf($loan, $template);
        }

        // ── Voie HTML (DomPDF) — pipeline existant ───────────────────────────
        return $this->generateWithDomPdf($loan, $locale);
    }

    private function generateWithDomPdf(LoanRequest $loan, string $locale): string
    {
        $html = $this->contractService->generateForClient($loan);

        $pdf = Pdf::loadHTML($html)
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled'      => false,
                      'defaultFont'          => 'DejaVu Sans',
                  ]);

        $filename = 'contract_' . $loan->reference . '_' . $locale . '.pdf';
        $path     = 'contracts/' . $filename;

        Storage::put($path, $pdf->output());

        return storage_path('app/' . $path);
    }
}
