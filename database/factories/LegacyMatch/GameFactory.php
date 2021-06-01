<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\LegacyMatch;

use App\Models\Beatmap;
use App\Models\LegacyMatch\Game;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition()
    {
        return [
            'beatmap_id' => Beatmap::factory(),
            'start_time' => Carbon::now()->subSeconds($beatmap->total_length),
            'play_mode' => fn(array $attributes) => Beatmap::find($attributes['beatmap_id'])->playmode,
            'scoring_type' => fn() => $this->faker->numberBetween(0, 3),
            'team_type' => fn() => $this->faker->numberBetween(0, 3),
        ];
    }

    public function inProgress()
    {
        return $this->state(['end_time' => null]);
    }

    public function complete()
    {
        return $this->state(['end_time' => now()]);
    }
}
