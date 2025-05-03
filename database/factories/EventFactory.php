<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'start_datetime' => Carbon::now(),
            'end_datetime' => Carbon::now(),
            'location' => $this->faker->word(),
            'category_id' => $this->faker->randomNumber(),
            'cost_for_members' => $this->faker->randomFloat(),
            'cost_for_guests' => $this->faker->randomFloat(),
            'max_participants' => $this->faker->word(),
            'guests_allowed' => $this->faker->boolean(),
            'max_guests_per_user' => $this->faker->randomNumber(),
            'user_id' => $this->faker->randomNumber(),
            'is_active' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
