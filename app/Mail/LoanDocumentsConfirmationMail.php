<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanDocumentsConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array  $data,
        public string $lang = 'fr'
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Vos documents ont bien été reçus',
            'en' => 'Your documents have been received',
            'es' => 'Sus documentos han sido recibidos',
            'pl' => 'Twoje dokumenty zostały odebrane',
        ];
        return new Envelope(subject: $subjects[$this->lang] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-documents-confirmation',
            with: ['data' => $this->data, 'lang' => $this->lang],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
