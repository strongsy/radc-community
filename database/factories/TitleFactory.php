<?php

namespace Database\Factories;

use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;

class TitleFactory extends Factory
{
    protected $model = Title::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Annual Gala',
                'Remembrance Day',
                'Charity Fundraiser',
                'Family Picnic',
                'Veterans Day Parade',
                'Memorial Service',
                'Community Outreach',
                'Educational Workshop',
                'Sports Tournament',
                'Holiday Celebration'
            ]),
            'description' => $this->faker->sentence(10),
        ];
    }
}
