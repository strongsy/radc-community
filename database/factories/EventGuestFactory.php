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
            'guest_id' => $this->faker->randomNumber(),
            'event_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
