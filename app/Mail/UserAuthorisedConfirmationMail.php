<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAuthorisedConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected User $user;

    public function __construct(User $user)
    {

        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Authorised Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-authorised-confirmation',
            with: [
                'user' => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
