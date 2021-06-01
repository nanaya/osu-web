<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

class TournamentFactory extends Factory
{
    protected $model = Tournament::class;

    public function definition()
    {
        return [
            'name' => fn() => "Such {$this->faker->word}",
            'description' => fn() => $this->faker->sentence,
            'play_mode' => 0,
            'rank_min' => 1,
            'rank_max' => 5000,
            'signup_open' => fn() => now(),
            'signup_close' => fn() => now()->addMonths(1),
            'start_date' => fn() => now()->addMonths(2),
            'end_date' => fn() => now()->addMonths(3),
        ];
    }
}
