<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\LegacyMatch;

use App\Models\LegacyMatch\LegacyMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyMatchFactory extends Factory
{
    protected $model = LegacyMatch::class;

    public function definition()
    {
        return [
            'name' => fn() => $this->faker->sentence(),
            'start_time' => fn() => now(),
            'private' => 0,
        ];
    }

    public function private()
    {
        return $this->state(['private' => 1]);
    }

    public function tourney()
    {
        return $this->state(['keep_forever' => 1]);
    }
}
