<?php

namespace Database\Factories;

use App\Models\Allergy;
use App\Models\DrinkPreference;
use App\Models\FoodPreference;
use App\Models\ParticipantDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ParticipantDetailFactory extends Factory
{
    protected $model = ParticipantDetail::class;

    public function definition(): array
    {
        $models = $this->faker->randomElement(['App\Models\Post', 'App\Models\Event', 'App\Models\Story', 'App\Models\Article']);
        return [
            'detailable_id' => $models::factory(),
            'detailable_type' => $models,
            'notes' => $this->faker->paragraphs(1, true),
            'allergy_id' => Allergy::factory(),
            'food_id' => FoodPreference::factory(),
            'drink_id' => DrinkPreference::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
