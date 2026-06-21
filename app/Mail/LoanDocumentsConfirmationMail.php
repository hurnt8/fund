<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanDocumentsConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data,
        public string $lang = 'fr'
    ) {}

    public function build(): static
    {
        return $this
            ->locale($this->lang)
            ->subject(__('message.docs_confirm_subject'))
            ->markdown('emails.loan-documents-confirmation');
    }
}
