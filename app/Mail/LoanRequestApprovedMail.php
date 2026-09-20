<?php

namespace App\Mail;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public LoanRequest $loan,
        public string      $mailLocale = 'fr',
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'fr' => 'Votre demande N°' . $this->loan->reference . ' a été approuvée — AURENZA CAPITAL',
            'en' => 'Your application N°' . $this->loan->reference . ' has been approved — AURENZA CAPITAL',
            'es' => 'Su solicitud N°' . $this->loan->reference . ' ha sido aprobada — AURENZA CAPITAL',
            'pl' => 'Wniosek nr ' . $this->loan->reference . ' został zatwierdzony — AURENZA CAPITAL',
        ];

        return new Envelope(subject: $subjects[$this->mailLocale] ?? $subjects['fr']);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan-approved',
            with: ['loan' => $this->loan, 'locale' => $this->mailLocale],
        );
    }
}
