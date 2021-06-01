<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\BeatmapFailtimes;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeatmapFailtimesFactory extends Factory
{
    protected $model = BeatmapFailtimes::class;

    public function definition()
    {
        $array = [];

        for ($i = 1; $i <= 100; $i++) {
            $field = 'p'.strval($i);
            $array[$field] = rand(1, 10000);
        }

        return $array;
    }

    public function fail()
    {
        return $this->state(['type' => 'fail']);
    }

    public function retry()
    {
        return $this->state(['type' => 'exit']);
    }
}
