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
        public string   $action  // 'approved' | 'rejected' | 'fee_required'
    ) {}

    public function envelope(): Envelope
    {
        $locale = $this->transfer->user->locale ?? 'fr';
        $ref    = $this->transfer->reference;
        $amount = number_format($this->transfer->amount, 2, ',', ' ') . ' ' . $this->transfer->currency;

        $subjects = [
            'approved' => [
                'fr' => "Virement {$ref} ({$amount}) — Validé",
                'en' => "Transfer {$ref} ({$amount}) — Approved",
                'es' => "Transferencia {$ref} ({$amount}) — Aprobada",
                'pl' => "Przelew {$ref} ({$amount}) — Zatwierdzony",
            ],
            'rejected' => [
                'fr' => "Virement {$ref} ({$amount}) — Rejeté",
                'en' => "Transfer {$ref} ({$amount}) — Rejected",
                'es' => "Transferencia {$ref} ({$amount}) — Rechazada",
                'pl' => "Przelew {$ref} ({$amount}) — Odrzucony",
            ],
            'fee_required' => [
                'fr' => "Virement {$ref} — Frais requis",
                'en' => "Transfer {$ref} — Fees required",
                'es' => "Transferencia {$ref} — Comisiones requeridas",
                'pl' => "Przelew {$ref} — Wymagane opłaty",
            ],
        ];

        $subject = $subjects[$this->action][$locale]
                ?? $subjects[$this->action]['fr']
                ?? "Mise à jour de votre virement {$ref}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transfer-action',
            with: [
                'transfer' => $this->transfer,
                'action'   => $this->action,
                'locale'   => $this->transfer->user->locale ?? 'fr',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
