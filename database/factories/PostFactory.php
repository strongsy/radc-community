<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inrandomOrder()->value('id'),
            'post_title' => $this->faker->sentence(),
            'post_content' => $this->faker->paragraphs(3, true),
            'status_id' => Status::inRandomOrder()->value('id'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
