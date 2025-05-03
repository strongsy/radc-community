<?php

namespace Database\Factories;

use App\Models\EventUsers;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventUsersFactory extends Factory
{
    protected $model = EventUsers::class;

    public function definition(): array
    {
        return [
            'event_id' => $this->faker->randomNumber(),
            'user_id' => $this->faker->randomNumber(),
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
