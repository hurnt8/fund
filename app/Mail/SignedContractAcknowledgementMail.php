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

    public function __construct(
        public LoanRequest $loan,
        public string      $locale = 'fr',
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Réception de votre contrat signé N°' . $this->loan->reference . ' — CREDIXA INVESTI',
            'pl' => 'Potwierdzenie otrzymania podpisanej umowy nr ' . $this->loan->reference,
            'en' => 'Receipt of your signed contract N°' . $this->loan->reference . ' — CREDIXA INVESTI',
            'es' => 'Recepción de su contrato firmado N°' . $this->loan->reference . ' — CREDIXA INVESTI',
        ];

        return new Envelope(subject: $subjects[$this->locale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.signed-contract-acknowledgement',
            with: ['loan' => $this->loan, 'locale' => $this->locale],
        );
    }
}
