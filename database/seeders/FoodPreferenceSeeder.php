<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class FoodPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('food_preferences')->insert([
            ['food_type' => 'Any', 'created_at' => now(), 'updated_at' => now()],
            ['food_type' => 'Non-Vegetarian', 'created_at' => now(), 'updated_at' => now()],
            ['food_type' => 'Vegetarian', 'created_at' => now(), 'updated_at' => now()],
            ['food_type' => 'Vegan', 'created_at' => now(), 'updated_at' => now()],
            ['food_type' => 'Pescetarian', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
