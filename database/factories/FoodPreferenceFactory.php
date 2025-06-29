<?php

namespace Database\Factories;

use App\Models\FoodPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodPreferenceFactory extends Factory
{
    protected $model = FoodPreference::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Vegetarian',
                'Vegan',
                'Halal',
                'Kosher',
                'Gluten-Free',
                'Keto',
                'Paleo',
                'Low-Sodium',
                'Diabetic-Friendly',
                'No Preference'
            ]),
            'description' => $this->faker->sentence(),
            'colour' => $this->faker->randomElement([
                'zinc', 'red', 'orange', 'amber', 'yellow', 'lime', 'green',
                'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'
            ]),
        ];
    }
}
