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
            'fr' => 'Facture ' . $ref . ' — AURENZA CAPITAL INVESTI',
            'en' => 'Invoice ' . $ref . ' — AURENZA CAPITAL INVESTI',
            'es' => 'Factura ' . $ref . ' — AURENZA CAPITAL INVESTI',
            'pl' => 'Faktura ' . $ref . ' — AURENZA CAPITAL INVESTI',
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
