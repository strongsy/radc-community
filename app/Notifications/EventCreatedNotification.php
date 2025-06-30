<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct(public Event $event
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Event: ' . $this->event->title->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new event has been created that might interest you.')
            ->line('**Event:** ' . $this->event->title->name)
            ->line('**Description:** ' . $this->event->description)
            ->line('**Venue:** ' . $this->event->venue->name)
            ->line('**Start Date:** ' . $this->event->start_date->format('d M Y g:i A'))
            ->line('**End Date:** ' . $this->event->end_date->format('d M Y g:i A'))
            ->action('View Event', route('events.show', $this->event))
            ->line('Thank you for being part of our community!');

    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'event_created',
            'event_id' => $this->event->id,
            'title' => $this->event->title->name,
            'message' => 'New event "' . $this->event->title->name . '" has been created.',
            'url' => route('events.show', $this->event),
        ];
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
