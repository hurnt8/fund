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

    public function __construct(
        public LoanRequest $loan,
        public string      $pdfPath,
        public string      $locale = 'fr',
        public string      $amortizationPdfPath = '',
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Validation de votre demande N°' . $this->loan->reference . ' — CREDIXA INVESTI',
            'pl' => 'Zatwierdzenie wniosku nr ' . $this->loan->reference . ' — CREDIXA INVESTI',
            'en' => 'Approval of your application N°' . $this->loan->reference . ' — CREDIXA INVESTI',
            'es' => 'Validación de su solicitud N°' . $this->loan->reference . ' — CREDIXA INVESTI',
        ];

        return new Envelope(subject: $subjects[$this->locale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan-validated',
            with: ['loan' => $this->loan, 'locale' => $this->locale],
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
