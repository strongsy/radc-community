<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Seeder;
use Random\RandomException;

class CategoryNewsSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $news = News::all();
        $newsCategories = NewsCategory::all();

        // Create events and attach random categories
        foreach ($news as $newsCat) {
            $randomStoryCategories = $newsCategories->random(random_int(1, 3))->pluck('id')->toArray();
            $newsCat->categories()->attach($randomStoryCategories);
        }
    }
}
