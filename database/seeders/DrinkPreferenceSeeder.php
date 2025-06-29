<?php

namespace Database\Seeders;

use App\Models\DrinkPreference;
use Illuminate\Database\Seeder;

class DrinkPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $preferences = [
            ['name' => 'White wine', 'description' => 'White wine only', 'colour' => 'green'],
            ['name' => 'Red wine', 'description' => 'Red wine only', 'colour' => 'emerald'],
            ['name' => 'Rose wine', 'description' => 'Rose wine only', 'colour' => 'blue'],
            ['name' => 'Lager', 'description' => 'Lager beer any type', 'colour' => 'purple'],
            ['name' => 'Bitter', 'description' => 'Bitter beer any type', 'colour' => 'orange'],
            ['name' => 'Non-alcoholic wine', 'description' => 'Non-alcoholic wine red or white', 'colour' => 'red'],
            ['name' => 'Non-alcoholic beer', 'description' => 'Non-alcoholic beer any type', 'colour' => 'amber'],
            ['name' => 'Soft drink', 'description' => 'Soft drink any type', 'colour' => 'cyan'],
            ['name' => 'Tap water', 'description' => 'Tap water', 'colour' => 'pink'],
            ['name' => 'Sparkling water', 'description' => 'Sparkling water any type', 'colour' => 'zinc'],
        ];

        foreach ($preferences as $preference) {
            DrinkPreference::create($preference);
        }
    }
}
