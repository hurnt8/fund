<?php

namespace App\Mail;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InsuranceAttestationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailLocale;

    public function __construct(
        public LoanRequest $loan,
        public string      $pdfPath,
        string             $locale = 'fr',
    ) {
        $this->mailLocale = $locale;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Votre attestation d\'assurance emprunteur — N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'en' => 'Your borrower insurance certificate — N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'pl' => 'Zaświadczenie ubezpieczenia kredytobiorcy — nr ' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'es' => 'Su certificado de seguro de prestatario — N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
        ];

        return new Envelope(
            subject: $subjects[$this->mailLocale] ?? $subjects['fr'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.insurance-attestation',
            with: [
                'loan'   => $this->loan,
                'locale' => $this->mailLocale,
            ],
        );
    }

    public function attachments(): array
    {
        if (!file_exists($this->pdfPath)) {
            return [];
        }

        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Assurance_' . $this->loan->reference . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
