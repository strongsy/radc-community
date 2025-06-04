<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Random\RandomException;

class CategoryEventSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $events = Event::all();
        $eventCategories = EventCategory::all();

        // Create events and attach random categories
        foreach ($events as $event) {
            $randomCategories = $eventCategories->random(random_int(1, 3))->pluck('id')->toArray();
            $event->categories()->attach($randomCategories);
        }
    }
}
