<?php

namespace Database\Seeders;

use App\Models\FoodPreference;
use Illuminate\Database\Seeder;

class FoodPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $preferences = [
            ['name' => 'Vegetarian', 'description' => 'No meat or fish', 'colour' => 'green'],
            ['name' => 'Vegan', 'description' => 'No animal products', 'colour' => 'emerald'],
            ['name' => 'Halal', 'description' => 'Halal dietary requirements', 'colour' => 'blue'],
            ['name' => 'Kosher', 'description' => 'Kosher dietary requirements', 'colour' => 'purple'],
            ['name' => 'Gluten-Free', 'description' => 'No gluten', 'colour' => 'orange'],
            ['name' => 'Keto', 'description' => 'Ketogenic diet', 'colour' => 'red'],
            ['name' => 'Paleo', 'description' => 'Paleolithic diet', 'colour' => 'amber'],
            ['name' => 'Low-Sodium', 'description' => 'Low sodium diet', 'colour' => 'cyan'],
            ['name' => 'Diabetic-Friendly', 'description' => 'Diabetic dietary needs', 'colour' => 'pink'],
            ['name' => 'No Preference', 'description' => 'No dietary restrictions', 'colour' => 'zinc'],
        ];

        foreach ($preferences as $preference) {
            FoodPreference::create($preference);
        }
    }
}
