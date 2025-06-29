<?php

namespace Database\Factories;

use App\Models\DrinkPreferenceUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DrinkPreferenceUserFactory extends Factory
{
    protected $model = DrinkPreferenceUser::class;

    public function definition(): array
    {
        return [
            'event_session_user_id' => $this->faker->randomNumber(),
            'drink_preference_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
