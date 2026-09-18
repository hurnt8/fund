<?php

namespace App\Mail;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanRequest $loan,
        public string      $contractPdfPath,
        public string      $amortizationPdfPath,
        public string      $locale = 'fr',
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Votre dossier de financement N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'pl' => 'Twój wniosek o finansowanie nr ' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'en' => 'Your financing file N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'es' => 'Su expediente de financiación N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
        ];

        return new Envelope(subject: $subjects[$this->locale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loan-created',
            with: ['loan' => $this->loan, 'locale' => $this->locale],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (file_exists($this->contractPdfPath)) {
            $attachments[] = Attachment::fromPath($this->contractPdfPath)
                ->as('Contrat_' . $this->loan->reference . '.pdf')
                ->withMime('application/pdf');
        }

        if (file_exists($this->amortizationPdfPath)) {
            $attachments[] = Attachment::fromPath($this->amortizationPdfPath)
                ->as('Tableau_Amortissement_' . $this->loan->reference . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
