<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Event;
use App\Notifications\EventCreatedNotification;
use Exception;
use Illuminate\Console\Command;


class TestEventNotificationCommand extends Command
{
    protected $signature = 'test:event-notification';
    protected $description = 'Test event notification';

    public function handle(): void
    {
        try {
            $user = User::where('is_subscribed', true)
                ->where('is_approved', true)
                ->where('is_blocked', false)
                ->whereNotNull('name')
                ->first();

            $event = Event::with(['title', 'venue'])->first();

            if (!$user || !$event) {
                $this->error('No suitable user or event found for testing');
                return;
            }

            $this->info("Testing notification for user: $user->name (ID: $user->id)");
            $this->info("Event: {$event->title?->name} (ID: $event->id)");

            // Create the notification instance first
            $notification = new EventCreatedNotification($event);

            // Send it synchronously (without a queue)
            $user->notify($notification);

            $this->info('Notification sent successfully!');

        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }

}
