<?php

namespace Database\Factories;

use App\Models\Dislike;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DislikeFactory extends Factory
{
    protected $model = Dislike::class;

    public function definition(): array
    {
        $models = $this->faker->randomElement(['App\Models\Post', 'App\Models\Event', 'App\Models\Story', 'App\Models\Article']);
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'dislikeable_id' => $models::factory(),
            'dislikeable_type' => $models,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
