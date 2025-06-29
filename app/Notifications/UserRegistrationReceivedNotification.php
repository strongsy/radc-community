<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistrationReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected User $user)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' - Registration Received!')
            ->greeting('Hello ' . $this->user->name . '!')
            ->line('Thank you for registering with ' . config('app.name') . '.')
            ->line('We have received your registration and your account is currently pending approval.')
            ->line('**Next Steps:**')
            ->line('1.Our Admin Team will verify your registration details.')
            ->line('2. If your account is approved, you will receive an email confirming successful approval.')
            ->line('3. You will receive another email notification requesting you to verify your email address.')
            ->line('**Important:** You will not be able to log in until both email verification and admin approval are complete.')
            ->line('If you have any questions or concerns, please don\'t hesitate to contact our support team.')
            ->line('Thank you for your patience!')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
        ];
    }
}
