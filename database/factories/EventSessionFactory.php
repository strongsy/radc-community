<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventSession;
use App\Models\EventSessionTitle;
use App\Models\EventSessionVenue;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventSessionFactory extends Factory
{
    protected $model = EventSession::class;

    public function definition(): array
    {
        $startTime = $this->faker->time();
        $endTime = $this->faker->time('H:i:s', strtotime($startTime . ' +2 hours'));

        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->sentence(4),
            'location' => $this->faker->company(),
            'description' => $this->faker->sentence(12),
            'start_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'capacity' => $this->faker->numberBetween(10, 100),
            'allow_guests' => $this->faker->boolean(60),
        ];
    }
}
