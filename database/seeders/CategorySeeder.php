<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Remembrance', 'description' => 'Remembrance event.', 'colour' => 'blue'],
            ['name' => 'Memorial Services', 'description' => 'Memorial and remembrance services.', 'colour' => 'zinc'],
            ['name' => 'Fundraising', 'description' => 'Fundraising events and activities.', 'colour' => 'green'],
            ['name' => 'Education', 'description' => 'Educational workshops and seminars.', 'colour' => 'purple'],
            ['name' => 'Sports', 'description' => 'Sporting activities.', 'colour' => 'orange'],
            ['name' => 'Curry', 'description' => 'Regional Curry Club event.', 'colour' => 'emerald'],
            ['name' => 'Family', 'description' => 'Family-friendly events and activities.', 'colour' => 'pink'],
            ['name' => 'Ceremonies', 'description' => 'Official ceremonies and celebrations.', 'colour' => 'red'],
            ['name' => 'Mess', 'description' => 'Mess functions.', 'colour' => 'amber'],
            ['name' => 'Workshops', 'description' => 'Training workshops and skill development.', 'colour' => 'cyan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
