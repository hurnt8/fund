<?php

namespace App\Services;

use App\Models\LoanRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LoanPdfService
{
    public function __construct(
        private ContractHtmlService $htmlService,
    ) {}

    /**
     * Génère le PDF du contrat et retourne son chemin absolu.
     */
    public function generate(LoanRequest $loan, string $locale = 'fr'): string
    {
        $loan->contract_language = $locale;
        return $this->htmlService->generate($loan);
    }

    /**
     * Génère le PDF du tableau d'amortissement et retourne son chemin absolu.
     */
    public function generateAmortizationPdf(LoanRequest $loan, string $locale = 'fr'): string
    {
        $translations = [
            'fr' => [
                'title'          => 'TABLEAU D\'AMORTISSEMENT',
                'header_sub'     => 'Organisme de financement — Solutions de crédit',
                'ref'            => 'Référence dossier',
                'date'           => 'Date',
                'amount'         => 'Montant du prêt',
                'duration'       => 'Durée',
                'months'         => 'mois',
                'monthly'        => 'Mensualité',
                'rate'           => 'Taux annuel',
                'total_interest' => 'Coût total des intérêts',
                'total_repaid'   => 'Total à rembourser',
                'col_month'      => 'Mois',
                'col_payment'    => 'Mensualité',
                'col_principal'  => 'Capital',
                'col_interest'   => 'Intérêts',
                'col_balance'    => 'Solde restant',
                'total'          => 'TOTAL',
                'footer'         => 'Document généré automatiquement',
            ],
            'en' => [
                'title'          => 'AMORTIZATION SCHEDULE',
                'header_sub'     => 'Financing institution — Credit solutions',
                'ref'            => 'File reference',
                'date'           => 'Date',
                'amount'         => 'Loan amount',
                'duration'       => 'Duration',
                'months'         => 'months',
                'monthly'        => 'Monthly payment',
                'rate'           => 'Annual rate',
                'total_interest' => 'Total interest cost',
                'total_repaid'   => 'Total to repay',
                'col_month'      => 'Month',
                'col_payment'    => 'Payment',
                'col_principal'  => 'Principal',
                'col_interest'   => 'Interest',
                'col_balance'    => 'Remaining balance',
                'total'          => 'TOTAL',
                'footer'         => 'Automatically generated document',
            ],
            'pl' => [
                'title'          => 'HARMONOGRAM SPŁAT',
                'header_sub'     => 'Instytucja finansowa — Rozwiązania kredytowe',
                'ref'            => 'Numer referencyjny',
                'date'           => 'Data',
                'amount'         => 'Kwota pożyczki',
                'duration'       => 'Okres',
                'months'         => 'miesięcy',
                'monthly'        => 'Miesięczna rata',
                'rate'           => 'Stopa roczna',
                'total_interest' => 'Łączny koszt odsetek',
                'total_repaid'   => 'Łączna kwota do spłaty',
                'col_month'      => 'Miesiąc',
                'col_payment'    => 'Płatność',
                'col_principal'  => 'Kapitał',
                'col_interest'   => 'Odsetki',
                'col_balance'    => 'Pozostałe saldo',
                'total'          => 'RAZEM',
                'footer'         => 'Dokument wygenerowany automatycznie',
            ],
            'es' => [
                'title'          => 'CUADRO DE AMORTIZACIÓN',
                'header_sub'     => 'Entidad financiera — Soluciones de crédito',
                'ref'            => 'Referencia del expediente',
                'date'           => 'Fecha',
                'amount'         => 'Importe del préstamo',
                'duration'       => 'Duración',
                'months'         => 'meses',
                'monthly'        => 'Cuota mensual',
                'rate'           => 'Tasa anual',
                'total_interest' => 'Coste total de intereses',
                'total_repaid'   => 'Total a reembolsar',
                'col_month'      => 'Mes',
                'col_payment'    => 'Pago',
                'col_principal'  => 'Capital',
                'col_interest'   => 'Intereses',
                'col_balance'    => 'Saldo restante',
                'total'          => 'TOTAL',
                'footer'         => 'Documento generado automáticamente',
            ],
        ];

        $texts    = $translations[$locale] ?? $translations['fr'];
        $schedule = $loan->amortization_schedule ?? [];

        $logoPath   = public_path('assets/images/logo-aurenza.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $html = view('pdfs.amortization-table', [
            'loan'        => $loan,
            'locale'      => $locale,
            'texts'       => $texts,
            'schedule'    => $schedule,
            'logoBase64'  => $logoBase64,
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'DejaVu Serif',
            ]);

        $filename = 'amortization_' . $loan->reference . '_' . $locale . '.pdf';
        $path     = 'contracts/' . $filename;

        Storage::disk('local')->makeDirectory('contracts');
        Storage::disk('local')->put($path, $pdf->output());

        return storage_path('app/' . $path);
    }
}
