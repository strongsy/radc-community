<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::factory(20)->create()->each(function ($event) {
            // Attach random categories
            $categories = Category::inRandomOrder()->limit(random_int(1, 3))->get();
            $event->categories()->attach($categories);
        });
    }
}
