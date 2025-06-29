<?php

namespace Database\Seeders;

use App\Models\EventSessionTitle;
use Illuminate\Database\Seeder;

class EventSessionTitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['name' => 'Opening Ceremony', 'description' => 'Event opening ceremony'],
            ['name' => 'Keynote Speech', 'description' => 'Keynote presentation'],
            ['name' => 'Panel Discussion', 'description' => 'Panel discussion session'],
            ['name' => 'Workshop Session', 'description' => 'Interactive workshop'],
            ['name' => 'Networking Break', 'description' => 'Networking and refreshments'],
            ['name' => 'Lunch Session', 'description' => 'Lunch and socializing'],
            ['name' => 'Closing Remarks', 'description' => 'Event closing remarks'],
            ['name' => 'Memorial Moment', 'description' => 'Moment of remembrance'],
            ['name' => 'Guest Speaker', 'description' => 'Special guest presentation'],
            ['name' => 'Award Ceremony', 'description' => 'Recognition and awards'],
        ];

        foreach ($titles as $title) {
            EventSessionTitle::create($title);
        }
    }
}
