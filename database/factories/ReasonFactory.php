<?php

namespace Database\Factories;

use App\Models\Reason;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReasonFactory extends Factory
{
    protected $model = Reason::class;

    public function definition(): array
    {
        return [
            'reason_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
