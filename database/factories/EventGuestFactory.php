<?php

namespace Database\Factories;

use App\Models\EventGuest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventGuestFactory extends Factory
{
    protected $model = EventGuest::class;

    public function definition(): array
    {
        return [
            'event_id' => $this->faker->randomNumber(),
            'invited_by' => $this->faker->randomNumber(),
            'guest_name' => $this->faker->name(),
            'guest_email' => $this->faker->unique()->safeEmail(),
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
