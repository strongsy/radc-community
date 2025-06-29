<?php

namespace Database\Factories;

use App\Models\EventSessionTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventSessionTitleFactory extends Factory
{
    protected $model = EventSessionTitle::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Opening Ceremony',
                'Keynote Speech',
                'Panel Discussion',
                'Workshop Session',
                'Networking Break',
                'Lunch Session',
                'Closing Remarks',
                'Memorial Moment',
                'Guest Speaker',
                'Award Ceremony'
            ]),
            'description' => $this->faker->sentence(8),
        ];
    }
}
