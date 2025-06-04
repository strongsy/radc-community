<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventTitle;
use App\Models\Status;
use App\Models\User;
use App\Models\Venue;
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

        $eventTitles = EventTitle::pluck('id')->toArray();


        // Calculate other dates relative to the starting date
        $closesAt = $startingDate->copy()->addMonths(2);
        $expiresAt = $startingDate->copy()->addYear();

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            /*'event_category_id' => EventCategory::inRandomOrder()->value('id'),*/
            'title_id' => fake()->randomElement($eventTitles),
            'event_content' => collect([
                '# ' . $this->faker->sentence(),
                '## ' . $this->faker->sentence(),
                $this->faker->paragraph(),
                '- ' . $this->faker->sentence(),
                '- ' . $this->faker->sentence(),
                '> ' . $this->faker->sentence(),
                '**' . $this->faker->words(3, true) . '**',
                '*' . $this->faker->words(2, true) . '*',
                $this->faker->paragraph(),
                '```',
                $this->faker->text(),
                '```'
            ])->join("\n\n"),
            'event_date' => $startingDate,
            'event_time' => $randomTime,
            'venue_id' => Venue::inRandomOrder()->value('id'),
            'status_id' => Status::inRandomOrder()->value('id'),
            'allow_guests' => $this->faker->boolean(20),
            'max_guests' => $this->faker->randomNumber(1),
            'max_attendees' => $this->faker->randomNumber(2),
            'user_cost' => $this->faker->randomFloat(2, 0, 999.99),  // 2 decimal places, max 999.99
            'guest_cost' => $this->faker->randomFloat(2, 0, 999.99), // 2 decimal places, max 999.99
            'cover_img' => $this->faker->imageUrl,
            'closes_at' => $closesAt,
            'expires_at' => $expiresAt,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
