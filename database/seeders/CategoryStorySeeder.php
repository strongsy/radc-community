<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\StoryCategory;
use Illuminate\Database\Seeder;
use Random\RandomException;

class CategoryStorySeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $stories = Story::all();
        $storyCategories = StoryCategory::all();

        foreach ($stories as $story) {
           $randomStoryCategory = $storyCategories->random(random_int(1, 3))->pluck('id')->toArray();
           $story->categories()->attach($randomStoryCategory);
        }
    }
}
