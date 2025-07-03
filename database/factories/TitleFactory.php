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
            $this->fromSeeded(),
        ];

    }

    /**
     * State for getting an existing title name from seeded data
     */
    public function fromSeeded(): static
    {
        return $this->state(function (array $attributes) {
            $seededNames = [
                'Southern Group Curry Club',
                'Northern Group Curry Club',
                'Western Group Curry Club',
                'Eastern Group Curry Club',
                'Midlands Group Curry Club',
                'Scotland Group Curry Club',
                'NI Group Curry Club',
                'Wales Group Curry Club',
                'RADC Officers Mess',
                'RADC Past & Present WO\'s & Sgt\'s Mess'
            ];

            $name = $this->faker->randomElement($seededNames);

            return [
                'name' => $name,
                'description' => match ($name) {
                    'Southern Group Curry Club' => 'The southern group curry club is a group curry club located in the southeast part of the UK.',
                    'Northern Group Curry Club' => 'The northern group curry club is a group curry club located in the north of the UK.',
                    'Western Group Curry Club' => 'The western group curry club is a group curry club located in the west of the UK.',
                    'Eastern Group Curry Club' => 'The eastern group curry club is a group curry club located in the east of the UK.',
                    'Midlands Group Curry Club' => 'The midlands group curry club is a group curry club located in the midlands.',
                    'Scotland Group Curry Club' => 'The Scottish group curry club is a group curry club located in the Scotland.',
                    'NI Group Curry Club' => 'The Northern Ireland group curry club is a group curry club located in the Northern Ireland.',
                    'Wales Group Curry Club' => 'The Welsh group curry club is a group curry club located in the Wales.',
                    'RADC Officers Mess' => 'The RADC Officers Mess is normally held in Litchfield and is open to all serving officers of the RAMS Dental Branch and Former RADC Retired Officers.',
                    'RADC Past & Present WO\'s & Sgt\'s Mess' => 'The RADC Past & Present WO\'s & Sgt\'s Mess is normally held in Litchfield and is open to all veteran WO\'s & Sgt\'s of the former RADC.',
                    //default => $this->faker->sentence(10)
                }
            ];
        });
    }

}
