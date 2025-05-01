<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SenderContactConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Email $email) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Sender Contact Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sender-contact-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
