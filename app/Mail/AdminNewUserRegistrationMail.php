<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewUserRegistrationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: 'Admin New User Registration',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-new-user-registration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
