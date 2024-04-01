<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Mail\Mailables\Address;

class AccountValidationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private string $email, private string $url)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Validate your account'
        );
        // return new Envelope(
        //     from: new Address('frdm.dev0@gmail.com', 'Franco Maciel'),
        //     replyTo: [
        //         new Address('damianmaciel0@gmail.com', 'Damian Maciel'),
        //     ],
        //     subject: 'Order Shipped',
        // );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.validateAccount',
            with: [
                'email' => $this->email,
                'url' => $this->url,
            ],
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
