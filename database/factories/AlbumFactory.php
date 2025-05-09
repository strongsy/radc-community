<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'gallery_id' => Gallery::factory(),
            'album_title' => $this->faker->sentence(),
            'album_desc' => $this->faker->paragraph(),
            'cover_img' => $this->faker->imageUrl(),
        ];
    }
}
