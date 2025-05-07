<?php

namespace Database\Factories;

use App\Models\ParticipantDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ParticipantDetailFactory extends Factory
{
    protected $model = ParticipantDetail::class;

    public function definition(): array
    {
        return [
            'detailable' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'allergy_id' => $this->faker->randomNumber(),
            'food_id' => $this->faker->randomNumber(),
            'drink_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
