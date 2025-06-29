<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Social Events',
                'Memorial Services',
                'Fundraising',
                'Education',
                'Sports & Recreation',
                'Community Service',
                'Family Events',
                'Ceremonies',
                'Meetings',
                'Workshops'
            ]),
            'description' => $this->faker->sentence(8),
            'colour' => $this->faker->randomElement([
                'zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green',
                'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'
            ]),
        ];
    }
}
