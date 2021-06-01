<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Build;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        $createdAt = now();

        return [
            'user_id' => User::factory(),
            'message' => fn() => $this->faker->paragraph(),
            // TODO: add support for more types
            'commentable_type' => 'build',
            'commentable_id' => Build::factory(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function deleted()
    {
        return $this->state(['deleted_at' => fn() => now()]);
    }
}
