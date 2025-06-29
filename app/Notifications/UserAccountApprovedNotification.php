<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected User $user, Authenticatable $approvedBy)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $loginUrl = route('login');

        return (new MailMessage)
            ->subject('Account Approved - Welcome to ' . config('app.name') . '!')
            ->greeting('Congratulations ' . $this->user->name . '!')
            ->line('Great news! Your account has been approved and you can now access ' . config('app.name') . '.')
            ->line('**Account Details:**')
            ->line('• **Email:** ' . $this->user->email)
            ->line('• **Approved on:** ' . $this->user->approved_at->format('F j, Y \a\t g:i A'))
            ->line('• **Approved by:** ' . $this->approvedBy->name)
            ->line('You can now log in to your account using your email and password.')
            ->action('Log In to Your Account', $loginUrl)
            ->line('**Getting Started:**')
            ->line('• Explore your dashboard and available features')
            ->line('• Update your profile information if needed')
            ->line('• Contact support if you have any questions')
            ->line('If you experience any issues logging in, please contact our support team.')
            ->line('Welcome aboard and thank you for joining us!')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');

    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
