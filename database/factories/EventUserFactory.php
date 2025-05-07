<?php

namespace Database\Factories;

use App\Models\EventUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventUserFactory extends Factory
{
    protected $model = EventUser::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'event_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
