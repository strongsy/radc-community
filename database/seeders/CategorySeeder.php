<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Social Events', 'description' => 'Social gatherings and community events', 'colour' => 'blue'],
            ['name' => 'Memorial Services', 'description' => 'Memorial and remembrance services', 'colour' => 'zinc'],
            ['name' => 'Fundraising', 'description' => 'Fundraising events and activities', 'colour' => 'green'],
            ['name' => 'Education', 'description' => 'Educational workshops and seminars', 'colour' => 'purple'],
            ['name' => 'Sports & Recreation', 'description' => 'Sports and recreational activities', 'colour' => 'orange'],
            ['name' => 'Community Service', 'description' => 'Community service and volunteer work', 'colour' => 'emerald'],
            ['name' => 'Family Events', 'description' => 'Family-friendly events and activities', 'colour' => 'pink'],
            ['name' => 'Ceremonies', 'description' => 'Official ceremonies and celebrations', 'colour' => 'red'],
            ['name' => 'Meetings', 'description' => 'Regular meetings and assemblies', 'colour' => 'amber'],
            ['name' => 'Workshops', 'description' => 'Training workshops and skill development', 'colour' => 'cyan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
