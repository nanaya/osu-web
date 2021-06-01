<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\UserMonthlyPlaycount;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserMonthlyPlaycountFactory extends Factory
{
    protected $model = UserMonthlyPlaycount::class;

    public function definition()
    {
        return [
            'year_month' => fn() => Carbon::instance($this->faker->dateTimeBetween('-6 years'))->format('ym'),
            'playcount' => fn() => $this->faker->numberBetween(500, 2000),
        ];
    }
}
