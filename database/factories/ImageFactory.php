<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inrandomOrder()->value('id'),
            'album_id' => Album::inrandomOrder()->value('id'),
            'img_path' => $this->faker->imageUrl(),
            'img_name' => $this->faker->words(3, true),
            'img_caption' => $this->faker->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
