<?php

namespace App\Mail;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferActionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Transfer $transfer,
        public string $action  // 'approved' | 'rejected' | 'fee_required'
    ) {}

    public function envelope(): Envelope
    {
        $ref    = $this->transfer->reference;
        $amount = number_format($this->transfer->amount, 2, ',', ' ') . ' ' . $this->transfer->currency;

        $subject = match ($this->action) {
            'approved'     => "Virement {$ref} ({$amount}) — Validé",
            'rejected'     => "Virement {$ref} ({$amount}) — Rejeté",
            'fee_required' => "Virement {$ref} — Frais requis",
            default        => "Mise à jour de votre virement {$ref}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.transfer-action',
            with: [
                'transfer' => $this->transfer,
                'action'   => $this->action,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
