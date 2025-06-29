<?php

namespace Database\Factories;

use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunityFactory extends Factory
{
    protected $model = Community::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Army Veterans',
                'Navy Veterans',
                'Air Force Veterans',
                'Marine Corps Veterans',
                'Coast Guard Veterans',
                'Vietnam Veterans',
                'Desert Storm Veterans',
                'Iraq War Veterans',
                'Afghanistan Veterans',
                'Peacekeeping Veterans'
            ]),
            'description' => $this->faker->sentence(10),
            'colour' => $this->faker->randomElement([
                'zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green',
                'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'
            ]),
        ];
    }
}
