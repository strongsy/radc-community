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
            'event_session_title_id' => EventSessionTitle::factory(),
            'event_session_venue_id' => EventSessionVenue::factory(),
            'description' => $this->faker->sentence(12),
            'start_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'allow_guests' => $this->faker->boolean(60),
        ];
    }
}
