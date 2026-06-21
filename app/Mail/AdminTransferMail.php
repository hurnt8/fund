<?php

namespace App\Mail;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminTransferMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $client,
        public Transfer $transfer
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau virement en attente — ' . $this->client->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-transfer',
        );
    }
}
