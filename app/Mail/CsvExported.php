<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CsvExported extends Mailable
{
    use Queueable, SerializesModels;
    
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */


    public function build()
    {
        return $this->view('emails.csv_exported')
        ->with(['message' => $this->message]);
    }
    public function envelope()
    {
        return new Envelope(
            subject: 'Transcolar - Notificação diária',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.csv_exported',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
