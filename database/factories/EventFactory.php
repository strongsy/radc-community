<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'event_title' => $this->faker->word(),
            'event_content' => $this->faker->word(),
            'event_date' => Carbon::now(),
            'event_time' => Carbon::now(),
            'event_loc' => $this->faker->word(),
            'event_cat' => $this->faker->randomNumber(),
            'event_status' => $this->faker->randomNumber(),
            'allow_guests' => $this->faker->boolean(),
            'max_guests' => $this->faker->randomNumber(),
            'max_attendees' => $this->faker->randomNumber(),
            'user_cost' => $this->faker->randomFloat(),
            'guest_cost' => $this->faker->randomFloat(),
            'cover_img' => $this->faker->word(),
            'closes_at' => Carbon::now(),
            'expires_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
