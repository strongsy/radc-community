<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\Post;
use App\Models\Article;
use App\Models\News;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Create stories
        Story::factory(30)->create()->each(function ($story) {
            $categories = Category::inRandomOrder()->limit(random_int(1, 3))->get();
            $story->categories()->attach($categories);
        });

        // Create posts
        Post::factory(50)->create()->each(function ($post) {
            $categories = Category::inRandomOrder()->limit(random_int(1, 2))->get();
            $post->categories()->attach($categories);
        });

        // Create articles
        Article::factory(25)->create()->each(function ($article) {
            $categories = Category::inRandomOrder()->limit(random_int(1, 3))->get();
            $article->categories()->attach($categories);
        });

        // Create news
        News::factory(20)->create()->each(function ($news) {
            $categories = Category::inRandomOrder()->limit(random_int(1, 2))->get();
            $news->categories()->attach($categories);
        });
    }
}
