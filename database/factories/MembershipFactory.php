<?php

namespace Database\Factories;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Bronze Member',
                'Silver Member',
                'Gold Member',
                'Platinum Member',
                'Life Member',
                'Honorary Member',
                'Associate Member',
                'Family Member'
            ]),
            'description' => $this->faker->sentence(8),
            'colour' => $this->faker->randomElement([
                'zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green',
                'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'
            ]),
        ];
    }
}
