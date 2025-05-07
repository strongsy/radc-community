<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    protected $model = Email::class;

    public function definition(): array
    {
        return [
            'sender_name' => $this->faker->name(),
            'sender_email' => $this->faker->unique()->safeEmail(),
            'email_subject' => $this->faker->words(10, true),
            'email_content' => $this->faker->paragraphs(5, true),
        ];
    }
}
