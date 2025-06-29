<?php

namespace Database\Factories;

use App\Models\DrinkPreferenceGuest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DrinkPreferenceGuestFactory extends Factory
{
    protected $model = DrinkPreferenceGuest::class;

    public function definition(): array
    {
        return [
            'event_session_guest_id' => $this->faker->randomNumber(),
            'drink_preference_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
