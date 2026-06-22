<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        $ref    = $this->invoice->reference;
        $locale = $this->invoice->client->locale ?? 'fr';

        $subjects = [
            'fr' => 'Facture ' . $ref . ' — CREDIXA INVESTI',
            'en' => 'Invoice ' . $ref . ' — CREDIXA INVESTI',
            'es' => 'Factura ' . $ref . ' — CREDIXA INVESTI',
            'pl' => 'Faktura ' . $ref . ' — CREDIXA INVESTI',
        ];

        return new Envelope(subject: $subjects[$locale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'locale'  => $this->invoice->client->locale ?? 'fr',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
