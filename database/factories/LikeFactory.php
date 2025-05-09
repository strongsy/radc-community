<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    public function definition(): array
    {
        $models = $this->faker->randomElement(['App\Models\Post', 'App\Models\Event', 'App\Models\Story', 'App\Models\Article']);
        return [
            'user_id' => User::inrandomOrder()->value('id'),
            'likeable_id' => $models::factory(),
            'likeable_type' => $models,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
