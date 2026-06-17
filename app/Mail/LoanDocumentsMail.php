<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanDocumentsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array  $data        Données du formulaire (name, email, address, doc_type…)
     * @param array  $attachments [['path'=>…,'name'=>…,'mime'=>…], …] — recto + éventuel verso
     * @param string $lang
     */
    public function __construct(
        public array  $data,
        public array  $attachments,
        public string $lang = 'fr'
    ) {}

    public function build(): static
    {
        $mail = $this
            ->replyTo($this->data['email'], $this->data['name'])
            ->subject(__('message.docs_subject') . ' — ' . $this->data['name'])
            ->markdown('emails.loan-documents');

        foreach ($this->attachments as $att) {
            $mail->attach($att['path'], [
                'as'   => $att['name'],
                'mime' => $att['mime'],
            ]);
        }

        return $mail;
    }
}
