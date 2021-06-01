<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Build;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildFactory extends Factory
{
    protected $model = Build::class;

    public function definition()
    {
        $streams = config('osu.changelog.update_streams');
        $streamCount = count($streams);

        return [
            'date' => $this->faker->dateTimeBetween('-5 years'),
            'stream_id' => fn() => $streams[rand(0, $streamCount - 1)],
            'users' => rand(100, 10000),
            'version' => fn(array $attributes) => Carbon::instance($attributes['date'])->format('Ymd'),
        ];
    }
}
