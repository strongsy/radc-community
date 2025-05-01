<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EmailFactory extends Factory
{
    protected $model = Email::class;

    public function definition(): array
    {
        return [
            'sender_name' => $this->faker->name(),
            'sender_email' => $this->faker->unique()->safeEmail(),
            'subject' => $this->faker->word(),
            'message' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
