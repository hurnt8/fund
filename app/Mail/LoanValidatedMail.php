<?php

namespace App\Mail;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanValidatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailLocale;
    public string $amortizationPdfPath;

    public function __construct(
        public LoanRequest $loan,
        public string      $pdfPath,
        string             $locale = 'fr',
        string             $amortizationPdfPath = '',
    ) {
        $this->mailLocale          = $locale;
        $this->amortizationPdfPath = $amortizationPdfPath;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Validation de votre demande N°' . $this->loan->reference . ' — AURENZA CAPITAL',
            'pl' => 'Zatwierdzenie wniosku nr ' . $this->loan->reference . ' — AURENZA CAPITAL',
            'en' => 'Approval of your application N°' . $this->loan->reference . ' — AURENZA CAPITAL',
            'es' => 'Validación de su solicitud N°' . $this->loan->reference . ' — AURENZA CAPITAL',
        ];

        return new Envelope(subject: $subjects[$this->mailLocale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-validated',
            with: ['loan' => $this->loan, 'locale' => $this->mailLocale],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (file_exists($this->pdfPath)) {
            $attachments[] = Attachment::fromPath($this->pdfPath)
                ->as('Contrat_' . $this->loan->reference . '.pdf')
                ->withMime('application/pdf');
        }

        if ($this->amortizationPdfPath && file_exists($this->amortizationPdfPath)) {
            $attachments[] = Attachment::fromPath($this->amortizationPdfPath)
                ->as('Tableau_Amortissement_' . $this->loan->reference . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
