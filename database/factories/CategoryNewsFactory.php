<?php

namespace Database\Factories;

use App\Models\CategoryNews;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryNewsFactory extends Factory
{
    protected $model = CategoryNews::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
