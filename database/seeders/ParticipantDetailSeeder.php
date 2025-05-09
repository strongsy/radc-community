<?php

namespace Database\Seeders;

use App\Models\ParticipantDetail;
use Illuminate\Database\Seeder;

class ParticipantDetailSeeder extends Seeder
{
    public function run(): void
    {
        ParticipantDetail::factory()->count(10)->create();
    }
}
