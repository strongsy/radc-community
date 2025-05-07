<?php

namespace Database\Factories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'gallery_id' => $this->faker->randomNumber(),
            'album_title' => $this->faker->word(),
            'album_desc' => $this->faker->word(),
            'cover_img' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
