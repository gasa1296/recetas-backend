<?php

namespace App\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Address, Envelope};
use Illuminate\Queue\SerializesModels;

class SignupMail extends Mailable
{
    use Queueable, SerializesModels;
    private $inputs;

    /**
     * Create a new message instance.
     */
    public function __construct($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Solicitud de registro",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.signup',
            with: [
                'inputs' => $this->inputs
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
