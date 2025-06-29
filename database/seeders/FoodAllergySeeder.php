<?php

namespace Database\Seeders;

use App\Models\FoodAllergy;
use Illuminate\Database\Seeder;

class FoodAllergySeeder extends Seeder
{
    public function run(): void
    {
        $allergies = [
            ['name' => 'Nuts', 'description' => 'Tree nuts allergy', 'colour' => 'red'],
            ['name' => 'Peanuts', 'description' => 'Peanut allergy', 'colour' => 'orange'],
            ['name' => 'Dairy', 'description' => 'Milk and dairy products', 'colour' => 'blue'],
            ['name' => 'Eggs', 'description' => 'Egg allergy', 'colour' => 'yellow'],
            ['name' => 'Shellfish', 'description' => 'Shellfish allergy', 'colour' => 'cyan'],
            ['name' => 'Fish', 'description' => 'Fish allergy', 'colour' => 'teal'],
            ['name' => 'Soy', 'description' => 'Soy allergy', 'colour' => 'green'],
            ['name' => 'Wheat', 'description' => 'Wheat allergy', 'colour' => 'amber'],
            ['name' => 'Sesame', 'description' => 'Sesame seed allergy', 'colour' => 'purple'],
            ['name' => 'Sulfites', 'description' => 'Sulfite sensitivity', 'colour' => 'pink'],
        ];

        foreach ($allergies as $allergy) {
            FoodAllergy::create($allergy);
        }
    }
}
