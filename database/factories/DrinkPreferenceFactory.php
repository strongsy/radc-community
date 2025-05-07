<?php

namespace Database\Factories;

use App\Models\DrinkPreference;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DrinkPreferenceFactory extends Factory
{
    protected $model = DrinkPreference::class;

    public function definition(): array
    {
        return [
            'drink_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
