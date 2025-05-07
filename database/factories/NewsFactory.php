<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'news_title' => $this->faker->word(),
            'news_content' => $this->faker->word(),
            'news_cat' => $this->faker->randomNumber(),
            'news_status' => $this->faker->randomNumber(),
            'release_at' => Carbon::now(),
            'expires_at' => Carbon::now(),
            'cover_img' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
