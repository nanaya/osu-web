<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Forum;

use App\Models\Forum\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'poster_id' => User::factory(),
            'post_username' => fn (array $attributes) => User::find($attributes['poster_id'])->username,
            'post_subject' => fn() => $this->faker->catchPhrase(),
            'post_text' => fn() => $this->faker->realtext(300),
            'post_time' => fn() => $this->faker->dateTimeBetween('-5 years'),
            'post_approved' => 1,
        ];
    }
}
