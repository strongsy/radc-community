<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'path' => 'images/placeholders/'.$this->faker->image('public/storage/images/placeholders', 640, 480, null, false),
            'caption' => $this->faker->optional(0.7)->sentence(),
            'imageable_id' => null,
            'imageable_type' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Configure the factory to create an image for a specific imageable model.
     */
    public function forImageable(Gallery $imageable): static
    {
        return $this->state([
            'imageable_id' => $imageable->getKey(),
            'imageable_type' => get_class($imageable),
        ]);
    }
}
