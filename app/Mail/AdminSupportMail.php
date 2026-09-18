<?php

namespace App\Mail;

use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminSupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $client,
        public SupportMessage $message
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message de ' . $this->client->name . ' — Support Aurenza Capital',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-support',
        );
    }
}
