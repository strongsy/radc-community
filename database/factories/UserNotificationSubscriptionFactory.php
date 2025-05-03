<?php

namespace Database\Factories;

use App\Models\UserNotificationSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserNotificationSubscriptionFactory extends Factory
{
    protected $model = UserNotificationSubscription::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'event_category_id' => $this->faker->randomNumber(),
            'is_subscribed' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
