<?php

namespace App\Console\Commands;

use App\Models\Event;
use Exception;
use Illuminate\Console\Command;

class CleanExpiredEventsCommand extends Command
{
    protected $signature = 'events:clean-expired';

    protected $description = 'Soft delete expired events';

    public function handle(): void
    {
        $this->info('Starting to clean expired events...');

        try {
            $before = Event::whereNull('deleted_at')->count();

            Event::cleanExpired();

            $after = Event::whereNull('deleted_at')->count();
            $deleted = $before - $after;

            $this->info("Successfully cleaned $deleted expired events");
        } catch (Exception $e) {
            $this->error("Error cleaning expired events: {$e->getMessage()}");
        }

    }
}
