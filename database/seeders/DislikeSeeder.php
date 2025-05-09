<?php

namespace Database\Seeders;

use App\Models\Dislike;
use Illuminate\Database\Seeder;

class DislikeSeeder extends Seeder
{
    public function run(): void
    {
        Dislike::factory()->count(5)->create();
    }
}
