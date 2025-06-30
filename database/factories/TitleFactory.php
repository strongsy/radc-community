<?php

namespace Database\Factories;

use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;

class TitleFactory extends Factory
{
    protected $model = Title::class;

    public function definition(): array
    {
        // This factory should mainly be used to reference existing titles
        // If you need to create a title in tests, use specific values
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(10),
        ];

    }

    /**
     * State for getting an existing title name from seeded data
     */
    public function fromSeeded(): static
    {
        return $this->state(function (array $attributes) {
            $seededNames = [
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
            ];

            $name = $this->faker->randomElement($seededNames);

            return [
                'name' => $name,
                'description' => match ($name) {
                    'Annual Gala' => 'Annual fundraising gala event',
                    'Remembrance Day' => 'Memorial service for fallen veterans',
                    'Charity Fundraiser' => 'Charity fundraising event',
                    'Family Picnic' => 'Annual family picnic gathering',
                    'Veterans Day Parade' => 'Veterans Day parade and ceremony',
                    'Memorial Service' => 'Memorial service for community members',
                    'Community Outreach' => 'Community outreach program',
                    'Educational Workshop' => 'Educational workshop series',
                    'Sports Tournament' => 'Annual sports tournament',
                    'Holiday Celebration' => 'Holiday celebration event',
                    default => $this->faker->sentence(10)
                }
            ];
        });
    }

}
