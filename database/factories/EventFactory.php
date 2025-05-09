<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Event;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Random\RandomException;

class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * @throws RandomException
     */
    public function definition(): array
    {
        // Create a random date within this year
        $startingDate = now()->startOfYear()->addDays(random_int(0, 365));

        $randomTime = '19:30:00';


        // Calculate other dates relative to the starting date
        $closesAt = $startingDate->copy()->addMonths(2);
        $expiresAt = $startingDate->copy()->addYear();



        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'event_title' => $this->faker->sentence(),
            'event_content' => $this->faker->paragraphs(3, true),
            'event_date' => $startingDate,
            'event_time' => $randomTime,
            'event_loc' => $this->faker->address(),
            'category_id' => Category::inRandomOrder()->value('id'),
            'status_id' => Status::inRandomOrder()->value('id'),
            'allow_guests' => $this->faker->boolean(20),
            'max_guests' => $this->faker->randomNumber(1),
            'max_attendees' => $this->faker->randomNumber(2),
            'user_cost' => $this->faker->randomNumber(4, true),
            'guest_cost' => $this->faker->randomNumber(4, true),
            'cover_img' => $this->faker->imageUrl,
            'closes_at' => $closesAt,
            'expires_at' => $expiresAt,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
