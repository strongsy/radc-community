<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => $this->faker->optional(0.8)->dateTime(),
            'password' => Hash::make('password'),
            'community_id' => Community::factory(),
            'membership_id' => Membership::factory(),
            'affiliation' => $this->faker->randomElement([
                'Army Veteran',
                'Navy Veteran',
                'Air Force Veteran',
                'Marines Veteran',
                'Coast Guard Veteran',
                'Spouse',
                'Family Member',
                'Supporter'
            ]),
            'is_approved' => $this->faker->boolean(70),
            'approved_at' => $this->faker->optional(0.7)->dateTime(),
            'approved_by' => null,
            'is_subscribed' => $this->faker->boolean(80),
            'is_blocked' => $this->faker->boolean(5),
            'unsubscribe_token' => Str::random(32),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            'approved_at' => $this->faker->dateTime(),
            'approved_by' => User::factory(),
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_blocked' => true,
        ]);
    }
}
