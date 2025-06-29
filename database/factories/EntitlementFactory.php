<?php

namespace Database\Factories;

use App\Models\Entitlement;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntitlementFactory extends Factory
{
    protected $model = Entitlement::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Event Access',
                'Gallery Upload',
                'Story Creation',
                'News Publication',
                'Article Writing',
                'Comment Moderation',
                'User Management',
                'Admin Access'
            ]),
            'description' => $this->faker->sentence(),
            'colour' => $this->faker->randomElement([
                'zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green',
                'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'
            ]),
        ];
    }
}
