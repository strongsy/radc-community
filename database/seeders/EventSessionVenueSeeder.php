<?php

namespace Database\Seeders;

use App\Models\EventSessionVenue;
use Illuminate\Database\Seeder;

class EventSessionVenueSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            ['name' => 'Main Hall', 'description' => 'Large main hall for events'],
            ['name' => 'Conference Room A', 'description' => 'Conference room for meetings'],
            ['name' => 'Conference Room B', 'description' => 'Secondary conference room'],
            ['name' => 'Auditorium', 'description' => 'Auditorium for presentations'],
            ['name' => 'Dining Hall', 'description' => 'Dining hall for meals'],
            ['name' => 'Garden Terrace', 'description' => 'Outdoor garden area'],
            ['name' => 'Library', 'description' => 'Library for quiet sessions'],
            ['name' => 'Workshop Room', 'description' => 'Room for workshops'],
            ['name' => 'Exhibition Hall', 'description' => 'Hall for exhibitions'],
            ['name' => 'Memorial Garden', 'description' => 'Memorial garden space'],
        ];

        foreach ($venues as $venue) {
            EventSessionVenue::create($venue);
        }
    }
}
