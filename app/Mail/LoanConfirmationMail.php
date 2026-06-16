<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data,
        public string $lang = 'fr'
    ) {}

    public function build(): static
    {
        return $this
            ->subject(__('message.loan_confirm_subject'))
            ->markdown('emails.loan-confirmation');
    }
}
