<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoanMail extends Mailable
{
    use Queueable, SerializesModels;
    public array $data;
    public $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data, $files = [])
    {
        $this->data = $data;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->data, $this->files);
        $email = $this->markdown('emails.loan')->subject(config("app.name")." - "."Demande de prêt");

        foreach ($this->files as $file) {
            $email->attach(Storage::path($file));
        }

        return $email;
    }
}
