<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Forum;

use App\Models\Forum\Forum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumFactory extends Factory
{
    protected $model = Forum::class;

    public function definition()
    {
        return [];
    }

    public function parent()
    {
        return $this->state([
            'forum_name' => fn() => $this->faker->catchPhrase(),
            'forum_desc' => fn() => $this->faker->realtext(80),
            'forum_type' => 0,
            'forum_parents' => [],
            'forum_rules' => '',
        ]);
    }

    public function child()
    {
        return $this->state([
            'forum_name' => fn() => $this->faker->catchPhrase(),
            'forum_desc' => fn() => $this->faker->realtext(80),
            'forum_type' => 1,
            'forum_parents' => [],
            'forum_rules' => '',
        ]);
    }
}
