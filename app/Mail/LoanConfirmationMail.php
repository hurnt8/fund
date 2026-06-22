<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array  $data,
        public string $lang = 'fr'
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Votre demande de prêt est en cours de traitement',
            'en' => 'Your loan request is being processed',
            'es' => 'Su solicitud de préstamo está siendo procesada',
            'pl' => 'Twój wniosek o pożyczkę jest przetwarzany',
        ];
        return new Envelope(subject: $subjects[$this->lang] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-confirmation',
            with: ['data' => $this->data, 'lang' => $this->lang],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
