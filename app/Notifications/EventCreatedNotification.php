<?php

namespace App\Notifications;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Log;

class EventCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $eventId;
    public string $eventTitle;
    public string $eventDescription;
    public string $venueName;
    public string $startDate;
    public string $endDate;

    public function __construct($event)
    {
        try {
            // Validate event exists and has required data
            if (!$event || !$event->id) {
                throw new InvalidArgumentException('Invalid event provided to notification');
            }

            // Load relationships first to ensure data is available
            $event->load(['title', 'venue']);

            // Store the actual data instead of the model with better validation
            $this->eventId = $event->id;
            $this->eventTitle = $event->title?->name ?? 'Event';
            $this->eventDescription = $event->description ?? 'No description available';
            $this->venueName = $event->venue?->name ?? 'TBD';

            // Add null checks for dates
            $this->startDate = $event->start_date ? $event->start_date->format('d M Y g:i A') : 'TBD';
            $this->endDate = $event->end_date ? $event->end_date->format('d M Y g:i A') : 'TBD';

        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('EventCreatedNotification constructor failed: ' . $e->getMessage(), [
                'event_id' => $event->id ?? 'unknown',
                'exception' => $e
            ]);

            // Set default values to prevent crashes
            $this->eventId = $event->id ?? 0;
            $this->eventTitle = 'Event';
            $this->eventDescription = 'No description available';
            $this->venueName = 'TBD';
            $this->startDate = 'TBD';
            $this->endDate = 'TBD';
        }
    }

    public function via($notifiable): array
    {
        return ['mail']; // Remove 'database' temporarily to avoid constraint issues
    }

    public function toMail($notifiable): MailMessage
    {
        // Safely get the first name with fallback
        $firstName = $this->getFirstName($notifiable);

        return (new MailMessage)
            ->subject('New Event: ' . $this->eventTitle)
            ->greeting('Hello ' . $firstName . '!')
            ->line('A new event has been created that might interest you.')
            ->line('**Event:** ' . $this->eventTitle)
            ->line('**Venue:** ' . $this->venueName)
            ->line('**Start Date:** ' . $this->startDate)
            ->line('**End Date:** ' . $this->endDate)
            ->line('**Description:**')
            ->line($this->eventDescription)
            ->action('View Event', $this->getEventUrl())
            ->line('Thank you for being part of our community!')
            ->line('If you no longer wish to receive these notifications, you can unsubscribe using the link below.')
            ->action('Unsubscribe', route('unsubscribe', $notifiable->unsubscribe_token));

    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'event_created',
            'event_id' => $this->eventId,
            'title' => $this->eventTitle,
            'message' => 'New event "' . $this->eventTitle . '" has been created.',
            'url' => $this->getEventUrl(),
        ];
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    /**
     * Safely get the first name from the notifiable user
     */
    private function getFirstName($notifiable): string
    {
        try {
            // Ensure the user model is fresh from a database
            if ($notifiable && method_exists($notifiable, 'fresh')) {
                $notifiable = $notifiable->fresh();
            }

            // Check if the name exists and is not empty
            if ($notifiable && !empty($notifiable->name)) {
                $nameParts = explode(' ', trim($notifiable->name));
                return $nameParts[0] ?? 'Friend';
            }

            return 'Friend';
        } catch (Exception $e) {
            Log::warning('Failed to get first name in EventCreatedNotification', [
                'user_id' => $notifiable->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return 'Friend';
        }
    }

    private function getEventUrl(): string
    {
        try {
            return route('events.show', $this->eventId);
        } catch (Exception) {
            return config('app.url') . '/events/' . $this->eventId;
        }
    }
}
