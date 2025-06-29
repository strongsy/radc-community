<?php

namespace Database\Factories;

use App\Models\FoodAllergy;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodAllergyFactory extends Factory
{
    protected $model = FoodAllergy::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Nuts',
                'Dairy',
                'Eggs',
                'Shellfish',
                'Fish',
                'Soy',
                'Wheat',
                'Sesame',
                'Sulfites',
                'Peanuts'
            ]),
            'description' => $this->faker->sentence(),
            'colour' => $this->faker->randomElement([
                'zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green',
                'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'
            ]),
        ];
    }
}
