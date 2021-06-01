<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\BeatmapPack;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeatmapPackFactory extends Factory
{
    protected $model = BeatmapPack::class;

    public function definition()
    {
        return [
            'url' => fn() => $this->faker->url(),
            'name' => fn() => $this->faker->catchPhrase(),
            'author' => fn() => $this->faker->username(),
            'tag' => fn() => $this->faker->randomElement(['S', 'T', 'A', 'R']).$this->faker->numberBetween(10, 100),
            'date' => fn() => now()->subMonths(2),
        ];
    }
}
