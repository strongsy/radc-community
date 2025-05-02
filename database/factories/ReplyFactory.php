<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReplyFactory extends Factory
{
    protected $model = Reply::class;

    public function definition(): array
    {
        return [
            // We'll set email_id during seeding instead of here
            'email_id' => null,
            'user_id' => User::factory(),
            'subject' => $this->faker->words(10, true),
            'message' => $this->faker->paragraphs(5, true),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function forEmail(Email $email): static
    {
        return $this->state(function (array $attributes) use ($email) {
            return [
                'email_id' => $email->id,
                // Ensure reply is created after the email
                'created_at' => $this->faker->dateTimeBetween($email->created_at, 'now'),
            ];
        });
    }
}
