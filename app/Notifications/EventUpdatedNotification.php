<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Event $event
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Event Updated: ' . $this->event->title->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('An event you might be interested in has been updated.')
            ->line('**Event:** ' . $this->event->title->name)
            ->line('**Venue:** ' . $this->event->venue->name)
            ->line('**Start Date:** ' . $this->event->start_date->format('M d, Y g:i A'))
            ->line('**Description:** ' . strip_tags($this->event->description))
            ->action('View Event', route('events.show', $this->event->id))
            ->line('Thank you for being part of our community!')
            ->line('If you no longer wish to receive these notifications, you can unsubscribe using the link below.')
            ->action('Unsubscribe', route('unsubscribe', $notifiable->unsubscribe_token));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title->name,
            'event_date' => $this->event->start_date,
        ];
    }
}
