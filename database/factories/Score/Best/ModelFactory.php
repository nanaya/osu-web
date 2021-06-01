<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Score\Best;

use App\Models\Beatmap;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

abstract class ModelFactory extends Factory
{
    public function configure()
    {
        return $this->afterMaking(function ($score) {
            if ($score->maxCombo !== null) {
                return;
            }

            $maxCombo = rand(1, $score->beatmap->countNormal);

            $score->fill([
                'maxcombo' => $maxCombo,
                'count300' => round($maxCombo * 0.8),
                'count100' => rand(0, round($maxCombo * 0.15)),
                'count50' => rand(0, round($maxCombo * 0.05)),
                'countgeki' => round($maxCombo * 0.3),
                'countmiss' => round($maxCombo * 0.05),
                'countkatu' => round($maxCombo * 0.05),
            ]);
        });
    }

    public function definition()
    {
        $pp = function (array $attributes) {
            $diff = Beatmap::find($attributes['beatmap_id'])->difficultyrating;

            return $this->faker->biasedNumberBetween(10, 100) * 1.5 * $diff;
        };

        return [
            'user_id' => User::factory(),
            'beatmap_id' => Beatmap::factory()->state([
                // force playmode to match score type
                'playmode' => Beatmap::modeInt($this->model::getMode()),
            ]),
            'score' => rand(50000, 100000000),
            'enabled_mods' => array_rand_val([0, 16, 24, 64, 72]),
            'date' => fn() => $this->faker->dateTimeBetween('-5 years'),
            'pp' => $pp,
            'rank' => array_rand_val(['A', 'S', 'B', 'SH', 'XH', 'X']),
        ];
    }

    public function withReplay()
    {
        return $this->state([
            'replay' => true,
        ])->afterCreating(function ($score) {
            $score->replayFile()->disk()->put($score->getKey(), 'this-is-totally-a-legit-replay');
        });
    }
}
