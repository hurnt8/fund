<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data,
        public string $lang = 'fr'
    ) {}

    public function build(): static
    {
        return $this
            ->replyTo($this->data['email'], $this->data['name'])
            ->subject(__('message.loan_admin_subject') . ' — ' . $this->data['name'])
            ->markdown('emails.loan');
    }
}
