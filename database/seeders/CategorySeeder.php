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
            ['name' => 'Memorial', 'description' => 'Memorial and remembrance services.', 'colour' => 'zinc'],
            ['name' => 'Fundraising', 'description' => 'Fundraising events and activities.', 'colour' => 'green'],
            ['name' => 'Education', 'description' => 'Educational workshops and seminars.', 'colour' => 'purple'],
            ['name' => 'Sports', 'description' => 'Sporting activities.', 'colour' => 'orange'],
            ['name' => 'Curry', 'description' => 'Regional Curry Club event.', 'colour' => 'emerald'],
            ['name' => 'Family', 'description' => 'Family-friendly events and activities.', 'colour' => 'pink'],
            ['name' => 'Ceremony', 'description' => 'Official ceremonies and celebrations.', 'colour' => 'red'],
            ['name' => 'Mess', 'description' => 'Mess functions.', 'colour' => 'amber'],
            ['name' => 'Workshops', 'description' => 'Training workshops and skill development.', 'colour' => 'cyan'],
            ['name' => 'Food', 'description' => 'Food and drink events.', 'colour' => 'teal'],
            ['name' => 'Community', 'description' => 'Community events and activities.', 'colour' => 'yellow'],
            ['name' => 'Adventure', 'description' => 'Adventure events and activities.', 'colour' => 'blue'],
            ['name' => 'Obituary', 'description' => 'Obituary services.', 'colour' => 'green'],
            ['name' => 'Church', 'description' => 'Church services.', 'colour' => 'purple'],
            ['name' => 'ToP', 'description' => 'Turning of the Page.', 'colour' => 'orange'],
            ['name' => 'Other', 'description' => 'Other events and activities.', 'colour' => 'sky'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
