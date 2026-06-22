<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountBlockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $unblockUrl;

    public function __construct(
        public readonly User   $user,
        public readonly string $token,
    ) {
        $this->unblockUrl = url('/account/unblock/' . $token);
    }

    public function envelope(): Envelope
    {
        $locale  = $this->user->locale ?? 'fr';
        $subject = __('auth.account_blocked_email_subject', [], $locale);
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.account-blocked');
    }

    public function attachments(): array
    {
        return [];
    }
}
