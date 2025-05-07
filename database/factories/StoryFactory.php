<?php

namespace Database\Factories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StoryFactory extends Factory
{
    protected $model = Story::class;

    public function definition(): array
    {
        return [
            'story_title' => $this->faker->word(),
            'story_content' => $this->faker->word(),
            'story_status' => $this->faker->randomNumber(),
            'story_cat' => $this->faker->randomNumber(),
            'cover_img' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
