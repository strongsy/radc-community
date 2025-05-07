<?php

namespace Database\Factories;

use App\Models\Allergy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AllergyFactory extends Factory
{
    protected $model = Allergy::class;

    public function definition(): array
    {
        return [
            'allergy_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
