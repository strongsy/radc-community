<?php

namespace Database\Factories;

use App\Models\EventNotification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventNotificationFactory extends Factory
{
    protected $model = EventNotification::class;

    public function definition(): array
    {
        return [
            'event_id' => $this->faker->randomNumber(),
            'type' => $this->faker->word(),
            'title' => $this->faker->word(),
            'message' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
