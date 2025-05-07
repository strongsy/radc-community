<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'album_id' => $this->faker->randomNumber(),
            'img_path' => $this->faker->word(),
            'img_name' => $this->faker->name(),
            'img_caption' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
