<?php

namespace Database\Seeders;

use App\Models\EventGuest;
use Illuminate\Database\Seeder;

class EventGuestSeeder extends Seeder
{
    public function run(): void
    {
        EventGuest::factory()->count(5)->create();
    }
}
