<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Beatmap;
use App\Models\Beatmapset;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeatmapFactory extends Factory
{
    protected $model = Beatmap::class;

    public function definition()
    {
        $hits = rand(100, 2000);
        $hitsSpinner = rand(0, 5);
        $hitsNormal = round(($hits - $hitsSpinner) * 0.9);
        $hitsSlider = $hits - $hitsSpinner - $hitsNormal;

        return [
            'filename' => fn() => $this->faker->sentence(3),
            'checksum' => str_repeat('0', 32),
            'version' => fn() => $this->faker->domainWord,
            'total_length' => rand(30, 200),
            'hit_length' => fn(array $attributes) => $attributes['total_length'] - rand(0, 20),
            'countNormal' => $hitsNormal,
            'countSlider' => $hitsSlider,
            'countSpinner' => $hitsSpinner,
            'bpm' => rand(100, 200),
            'diff_drain' => rand(0, 10),
            'diff_size' => rand(0, 10),
            'diff_overall' => rand(0, 10),
            'diff_approach' => rand(0, 10),
            'playmode' => array_rand_val(Beatmap::MODES),
            'approved' => array_rand_val(Beatmapset::STATES),
            'difficultyrating' => rand(0, 5000) / 1000,
            'playcount' => rand(0, 50000),
            'passcount' => fn(array $attributes) => round($attributes['playcount'] * 0.7),
        ];
    }

    public function qualified()
    {
        return $this->state(['approved' => Beatmapset::STATES['qualified']]);
    }

    public function ranked()
    {
        return $this->state(['approved' => Beatmapset::STATES['ranked']]);
    }

    public function wip()
    {
        return $this->state(['approved' => Beatmapset::STATES['wip']]);
    }
}
