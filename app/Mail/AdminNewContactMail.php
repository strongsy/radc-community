<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Email $email) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Admin New Contact Us Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-new-contact',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
