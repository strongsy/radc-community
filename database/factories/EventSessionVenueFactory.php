<?php

namespace Database\Factories;

use App\Models\EventSessionVenue;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventSessionVenueFactory extends Factory
{
    protected $model = EventSessionVenue::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Main Hall',
                'Conference Room A',
                'Conference Room B',
                'Auditorium',
                'Dining Hall',
                'Garden Terrace',
                'Library',
                'Workshop Room',
                'Exhibition Hall',
                'Memorial Garden'
            ]),
            'description' => $this->faker->sentence(),
        ];
    }
}
