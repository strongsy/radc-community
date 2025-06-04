<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->command('events:clean-expired')
                ->daily()
                ->at('00:00')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/event-cleanup.log'));
        });

    }
}
