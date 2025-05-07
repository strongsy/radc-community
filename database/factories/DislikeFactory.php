<?php

namespace Database\Factories;

use App\Models\Dislike;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DislikeFactory extends Factory
{
    protected $model = Dislike::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->word(),
            'dislikeable' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
