<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'commentable_id' => null, // Will be set when using for() method
            'commentable_type' => null, // Will be set when using for() method
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Configure the factory to create a comment for a specific commentable model.
     */
    public function forCommentable(Comment $commentable): static
    {
        return $this->state([
            'commentable_id' => $commentable->getKey(),
            'commentable_type' => get_class($commentable),
        ]);
    }
}
