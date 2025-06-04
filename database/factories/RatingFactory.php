<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        $models = $this->faker->randomElement(['App\Models\Post', 'App\Models\Event', 'App\Models\Story', 'App\Models\Article']);

        return [
            'user_id' => User::inrandomOrder()->value('id'),
            'rateable_id' => $models::inRandomOrder()->value('id'),
            'rateable_type' => $this->faker->word(),
            'rating' => $this->faker->randomelement([1, 2, 3, 4, 5]),
            'rating_review' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
