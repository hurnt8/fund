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
        $html = $this->embedStorageImages($html);

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

    /**
     * Convertit les URLs storage/ en data URIs base64 pour DomPDF (qui n'accepte pas les URLs HTTP).
     * Gère les attributs src="..." et les CSS url("...").
     */
    private function embedStorageImages(string $html): string
    {
        $storagePublicPath = storage_path('app/public/');
        $storagePublicUrl  = url('storage/');

        $callback = function (string $url) use ($storagePublicPath, $storagePublicUrl): string {
            if (!str_starts_with($url, $storagePublicUrl)) {
                return $url;
            }
            $relativePath = ltrim(substr($url, strlen($storagePublicUrl)), '/');
            $filePath     = $storagePublicPath . $relativePath;
            if (!file_exists($filePath)) {
                return $url;
            }
            $mime = mime_content_type($filePath) ?: 'image/png';
            $data = base64_encode(file_get_contents($filePath));
            return 'data:' . $mime . ';base64,' . $data;
        };

        // Remplace src="http://...storage/..."
        $html = preg_replace_callback(
            '/\bsrc="(' . preg_quote($storagePublicUrl, '/') . '[^"]+)"/i',
            fn ($m) => 'src="' . $callback($m[1]) . '"',
            $html
        );

        // Remplace url("http://...storage/...") dans les styles CSS inline
        $html = preg_replace_callback(
            '/url\(["\']?(' . preg_quote($storagePublicUrl, '/') . '[^"\')\s]+)["\']?\)/i',
            fn ($m) => 'url("' . $callback($m[1]) . '")',
            $html
        );

        return $html;
    }

    /**
     * Génère le PDF du tableau d'amortissement et retourne son chemin absolu.
     */
    public function generateAmortizationPdf(LoanRequest $loan, string $locale = 'fr'): string
    {
        $translations = [
            'fr' => [
                'title'         => 'TABLEAU D\'AMORTISSEMENT',
                'header_sub'    => 'Organisme de financement — Solutions de crédit',
                'ref'           => 'Référence dossier',
                'date'          => 'Date',
                'amount'        => 'Montant du prêt',
                'duration'      => 'Durée',
                'months'        => 'mois',
                'monthly'       => 'Mensualité',
                'rate'          => 'Taux annuel',
                'total_interest'=> 'Coût total des intérêts',
                'total_repaid'  => 'Total à rembourser',
                'col_month'     => 'Mois',
                'col_payment'   => 'Mensualité',
                'col_principal' => 'Capital',
                'col_interest'  => 'Intérêts',
                'col_balance'   => 'Solde restant',
                'total'         => 'TOTAL',
                'footer'        => 'Document généré automatiquement',
            ],
            'en' => [
                'title'         => 'AMORTIZATION SCHEDULE',
                'header_sub'    => 'Financing institution — Credit solutions',
                'ref'           => 'File reference',
                'date'          => 'Date',
                'amount'        => 'Loan amount',
                'duration'      => 'Duration',
                'months'        => 'months',
                'monthly'       => 'Monthly payment',
                'rate'          => 'Annual rate',
                'total_interest'=> 'Total interest cost',
                'total_repaid'  => 'Total to repay',
                'col_month'     => 'Month',
                'col_payment'   => 'Payment',
                'col_principal' => 'Principal',
                'col_interest'  => 'Interest',
                'col_balance'   => 'Remaining balance',
                'total'         => 'TOTAL',
                'footer'        => 'Automatically generated document',
            ],
            'pl' => [
                'title'         => 'HARMONOGRAM SPŁAT',
                'header_sub'    => 'Instytucja finansowa — Rozwiązania kredytowe',
                'ref'           => 'Numer referencyjny',
                'date'          => 'Data',
                'amount'        => 'Kwota pożyczki',
                'duration'      => 'Okres',
                'months'        => 'miesięcy',
                'monthly'       => 'Miesięczna rata',
                'rate'          => 'Stopa roczna',
                'total_interest'=> 'Łączny koszt odsetek',
                'total_repaid'  => 'Łączna kwota do spłaty',
                'col_month'     => 'Miesiąc',
                'col_payment'   => 'Płatność',
                'col_principal' => 'Kapitał',
                'col_interest'  => 'Odsetki',
                'col_balance'   => 'Pozostałe saldo',
                'total'         => 'RAZEM',
                'footer'        => 'Dokument wygenerowany automatycznie',
            ],
            'es' => [
                'title'         => 'CUADRO DE AMORTIZACIÓN',
                'header_sub'    => 'Entidad financiera — Soluciones de crédito',
                'ref'           => 'Referencia del expediente',
                'date'          => 'Fecha',
                'amount'        => 'Importe del préstamo',
                'duration'      => 'Duración',
                'months'        => 'meses',
                'monthly'       => 'Cuota mensual',
                'rate'          => 'Tasa anual',
                'total_interest'=> 'Coste total de intereses',
                'total_repaid'  => 'Total a reembolsar',
                'col_month'     => 'Mes',
                'col_payment'   => 'Pago',
                'col_principal' => 'Capital',
                'col_interest'  => 'Intereses',
                'col_balance'   => 'Saldo restante',
                'total'         => 'TOTAL',
                'footer'        => 'Documento generado automáticamente',
            ],
        ];

        $texts    = $translations[$locale] ?? $translations['fr'];
        $schedule = $loan->amortization_schedule ?? [];

        $html = view('pdfs.amortization-table', [
            'loan'     => $loan,
            'locale'   => $locale,
            'texts'    => $texts,
            'schedule' => $schedule,
        ])->render();

        $pdf = Pdf::loadHTML($html)
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled'      => false,
                      'defaultFont'          => 'DejaVu Sans',
                  ]);

        $filename = 'amortization_' . $loan->reference . '_' . $locale . '.pdf';
        $path     = 'contracts/' . $filename;

        Storage::put($path, $pdf->output());

        return storage_path('app/' . $path);
    }
}
