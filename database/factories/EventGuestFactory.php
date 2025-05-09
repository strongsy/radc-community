<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventGuestFactory extends Factory
{
    protected $model = EventGuest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inrandomOrder()->value('id'),
            'event_id' => Event::inrandomOrder()->value('id'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
