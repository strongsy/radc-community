<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Title;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+6 months');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +7 days');
        $rsvpCloses = $this->faker->dateTimeBetween('now', $startDate);

        return [
            'user_id' => User::factory(),
            'title_id' => Title::factory(),
            'venue_id' => Venue::factory(),
            'description' => $this->faker->paragraphs(3, true),
            'max_serials' => $this->faker->numberBetween(1, 40),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'rsvp_closes_at' => $rsvpCloses->format('Y-m-d'),
        ];
    }
}
