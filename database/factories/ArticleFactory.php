<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'article_title' => $this->faker->word(),
            'article_content' => $this->faker->word(),
            'article_cat' => $this->faker->word(),
            'article_status' => $this->faker->word(),
            'cover_img' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
