<?php

namespace Database\Factories;

use App\Models\CategoryStory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryStoryFactory extends Factory
{
    protected $model = CategoryStory::class;

    public function definition(): array
    {
        return [
            'story_id' => $this->faker->randomNumber(),
            'story_category_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
