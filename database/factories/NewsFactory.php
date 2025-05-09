<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\News;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inrandomOrder()->value('id'),
            'news_title' => $this->faker->sentence(),
            'news_content' => $this->faker->paragraphs(3, true),
            'news_cat' => Category::inRandomOrder()->value('id'),
            'news_status' => Status::inRandomOrder()->value('id'),
            'release_at' => Carbon::now(),
            'expires_at' => Carbon::now(),
            'cover_img' => $this->faker->imageUrl(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
