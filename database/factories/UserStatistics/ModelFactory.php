<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\UserStatistics;

use Illuminate\Database\Eloquent\Factories\Factory;

abstract class ModelFactory extends Factory
{
    public function definition()
    {
        return $this->generateStats();
    }

    private function generateStats()
    {
        return [
            'level' => rand(1, 104),
            'count300' => rand(10000, 5000000), // 10k to 5mil
            'count100' => rand(10000, 2000000), // 10k to 2mil
            'count50' => rand(10000, 1000000), // 10k to 1mil
            'countMiss' => rand(10000, 1000000), // 10k to 1mil
            'accuracy_total' => rand(1000, 250000), // 1k to 250k. unsure what field is for
            'accuracy_count' => rand(1000, 250000), // 1k to 250k. unsure what field is for
            'accuracy' => fn(array $attributes) => $attributes['accuracy_new'] / 100,
            'accuracy_new' => (float) (rand(850000, 1000000)) / 10000, // 85.0000 - 100.0000
            'playcount' => rand(1000, 250000), // 1k - 250k
            'fail_count' => fn(array $attributes) => rand($attributes['playcount'] * 0.1, $attributes['playcount'] * 0.2),
            'exit_count' => fn(array $attributes) => rand($attributes['playcount'] * 0.2, $attributes['playcount'] * 0.3),
            'rank' => rand(1, 500000),
            'ranked_score' => (float) rand(500000, 2000000000) * 2, // 500k - 4bil
            'total_score' => fn(array $attributes) => $attributes['ranked_score'] * 1.4,
            'total_seconds_played' => fn(array $attributes) => rand($attributes['playcount'] * 120 * 0.3, $attributes['playcount'] * 120 * 0.7),
            'x_rank_count' => fn(array $attributes) => round($attributes['playcount'] * 0.001),
            'xh_rank_count' => fn(array $attributes) => round($attributes['playcount'] * 0.0003),
            's_rank_count' => fn(array $attributes) => round($attributes['playcount'] * 0.05),
            'sh_rank_count' => fn(array $attributes) => round($attributes['playcount'] * 0.02),
            'a_rank_count' => fn(array $attributes) => round($attributes['playcount'] * 0.2),
            'rank_score' => (float) rand(1, 15000),
            'rank_score_index' => rand(1, 500000),
            'max_combo' => rand(500, 4000),
        ];
    }
}
