<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Changelog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChangelogFactory extends Factory
{
    protected $model = Changelog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'prefix' => fn() => $this->faker->randomElement(['*', '+', '?']),
            'category' => fn() => $this->faker->randomElement(['Web', 'Audio', 'Code', 'Editor', 'Gameplay', 'Graphics']),
            'message' => fn() => $this->faker->catchPhrase(),
            'checksum' => fn() => $this->faker->md5,
            'date' => fn() => $this->faker->dateTimeBetween('-6 weeks'),
        ];
    }
}
