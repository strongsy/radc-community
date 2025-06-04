<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;
use Random\RandomException;

class CategoryArticleSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $articles = Article::all();
        $articleCategories = ArticleCategory::all();

        foreach ($articles as $article) {
            $randomArticleCategory = $articleCategories->random(random_int(1, 3))->pluck('id')->toArray();
            $article->categories()->attach($randomArticleCategory);
        }
    }
}
