<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\Story;
use App\Models\StoryCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StoryFactory extends Factory
{
    protected $model = Story::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inrandomOrder()->value('id'),
            'story_title' => $this->faker->sentence(),
            'story_content' => $this->faker->paragraphs(3, true),
            'story_status' => Status::inrandomOrder()->value('id'),
            'story_category_id' => StoryCategory::inrandomOrder()->value('id'),
            'cover_img' => $this->faker->imageUrl(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
