<?php

namespace Database\Factories;

use App\Models\CategoryArticle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryArticleFactory extends Factory
{
    protected $model = CategoryArticle::class;

    public function definition(): array
    {
        return [
            'article_id' => $this->faker->randomNumber(),
            'article_category_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
