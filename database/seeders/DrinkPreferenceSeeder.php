<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class DrinkPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('drink_preferences')->insert([
            ['drink_type' => 'None', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Alcoholic', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Non-Alcoholic', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Mixed', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Red Wine', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'White Wine', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Sparkling Wine', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Rose Wine', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Beer', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Soft Drinks', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Juice', 'created_at' => now(), 'updated_at' => now()],
            ['drink_type' => 'Other', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
