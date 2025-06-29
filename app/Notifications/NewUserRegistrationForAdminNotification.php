<?php

namespace App\Notifications;

use App\Models\Community;
use App\Models\Entitlement;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistrationForAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int|null $community_id = null;
    public int|null $membership_id = null;
    public string $affiliation = '';
    public bool $is_subscribed = false;
    public array $communities = [];
    public array $memberships = [];
    public array $entitlements = [];
    // Array to store selected entitlements
    public array $selectedEntitlements = [];


    public function __construct(public User $user)
    {
        $this->communities = Community::pluck('name', 'id')->toArray();
        $this->memberships = Membership::pluck('name', 'id')->toArray();
        $this->entitlements = Entitlement::pluck('name', 'id')->toArray();

    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {

        $approvalUrl = route('users.registrations');

        return (new MailMessage)
            ->subject('New User Registration - Action Required')
            ->greeting('Hello Admin!')
            ->line('A new user has registered on ' . config('app.name') . ' and is awaiting approval.')
            ->line('**User Details:**')
            ->line('• **Name:** ' . $this->user->name)
            ->line('• **Email:** ' . $this->user->email)
            ->line('• **Registered:** ' . $this->user->created_at->format('F j, Y \a\t g:i A'))
            ->line('• **Email Verified:** ' . ($this->user->hasVerifiedEmail() ? 'Yes' : 'Pending'))
            ->line('Please review and approve this user account when appropriate.')
            ->action('Review User Registration', $approvalUrl)
            ->line('You can approve or reject this registration from the admin panel.')
            ->line('**Note:** The user will only be able to log in after both email verification and admin approval are complete.')
            ->salutation('Best regards, ' . config('app.name') . ' System');
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
