<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Forum;

use App\Models\Forum\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
    protected $model = Topic::class;

    public function definition()
    {
        return [
            'topic_poster' => User::factory(),
            'topic_first_poster_name' => fn (array $attributes) => User::find($attributes['topic_poster'])->username,
            'topic_title' => fn() => $this->faker->catchPhrase(),
            'topic_views' => rand(0, 99999),
            'topic_approved' => 1,
            'topic_time' => fn() => $this->faker->dateTimeBetween('-5 years'),
        ];
    }
}
