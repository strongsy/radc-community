<?php

namespace Database\Factories;

use App\Models\FoodPreference;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FoodPreferenceFactory extends Factory
{
    protected $model = FoodPreference::class;

    public function definition(): array
    {
        return [
            'food_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
