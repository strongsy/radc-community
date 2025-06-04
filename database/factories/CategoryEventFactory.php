<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryEvent;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryEventFactory extends Factory
{
    protected $model = CategoryEvent::class;

    public function definition(): array
    {
        return [
            'event_category_id' => EventCategory::inRandomOrder()->value('id'),
            'event_id' => Event::inRandomOrder()->value('id'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
