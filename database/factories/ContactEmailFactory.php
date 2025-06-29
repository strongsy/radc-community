<?php

namespace Database\Factories;

use App\Models\ContactEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactEmailFactory extends Factory
{
    protected $model = ContactEmail::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(4),
            'message' => $this->faker->paragraphs(3, true),
        ];
    }
}
