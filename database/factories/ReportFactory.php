<?php

namespace Database\Factories;

use App\Models\Reason;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $models = $this->faker->randomElement(['App\Models\Post', 'App\Models\Event', 'App\Models\Story', 'App\Models\Article']);

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'reportable_id' => $models::inRandomOrder()->value('id'),
            'reportable_type' => $models,
            'reportable_reason' => Reason::inRandomOrder()->value('reason_type'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
