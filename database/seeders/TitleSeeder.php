<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            ['name' => 'Annual Gala', 'description' => 'Annual fundraising gala event'],
            ['name' => 'Remembrance Day', 'description' => 'Memorial service for fallen veterans'],
            ['name' => 'Charity Fundraiser', 'description' => 'Charity fundraising event'],
            ['name' => 'Family Picnic', 'description' => 'Annual family picnic gathering'],
            ['name' => 'Veterans Day Parade', 'description' => 'Veterans Day parade and ceremony'],
            ['name' => 'Memorial Service', 'description' => 'Memorial service for community members'],
            ['name' => 'Community Outreach', 'description' => 'Community outreach program'],
            ['name' => 'Educational Workshop', 'description' => 'Educational workshop series'],
            ['name' => 'Sports Tournament', 'description' => 'Annual sports tournament'],
            ['name' => 'Holiday Celebration', 'description' => 'Holiday celebration event'],
        ];

        foreach ($titles as $title) {
            Title::create($title);
        }
    }
}
