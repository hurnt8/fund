<?php

namespace App\Mail;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignedContractAcknowledgementMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailLocale;

    public function __construct(
        public LoanRequest $loan,
        string             $locale = 'fr',
    ) {
        $this->mailLocale = $locale;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Réception de votre contrat signé N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'pl' => 'Potwierdzenie otrzymania podpisanej umowy nr ' . $this->loan->reference,
            'en' => 'Receipt of your signed contract N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
            'es' => 'Recepción de su contrato firmado N°' . $this->loan->reference . ' — AURENZA CAPITAL INVESTI',
        ];

        return new Envelope(subject: $subjects[$this->mailLocale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.signed-contract-acknowledgement',
            with: ['loan' => $this->loan, 'locale' => $this->mailLocale],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
