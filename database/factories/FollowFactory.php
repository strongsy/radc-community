<?php

namespace Database\Factories;

use App\Models\Follow;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FollowFactory extends Factory
{
    protected $model = Follow::class;

    public function definition(): array
    {
        return [
            'follower_id' => $this->faker->randomNumber(),
            'following_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
