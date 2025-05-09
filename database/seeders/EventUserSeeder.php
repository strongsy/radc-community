<?php

namespace Database\Seeders;

use App\Models\EventUser;
use Illuminate\Database\Seeder;

class EventUserSeeder extends Seeder
{
    public function run(): void {
        EventUser::factory()->count(5)->create();
    }
}
