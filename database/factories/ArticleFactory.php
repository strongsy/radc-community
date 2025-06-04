<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'article_title' => $this->faker->sentence(),
            'article_content' => $this->faker->paragraphs(5, true),
            'article_category_id' => ArticleCategory::inRandomOrder()->value('id'),
            /*'category_id' => Category::inRandomOrder()->value('id'),*/
            'status_id' => Status::inRandomOrder()->value('id'),
            'cover_img' => $this->faker->imageUrl(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
