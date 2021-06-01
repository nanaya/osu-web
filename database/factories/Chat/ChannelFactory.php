<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Chat;

use App\Models\Chat\Channel;
use App\Models\LegacyMatch\LegacyMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition()
    {
        return [
            'name' => fn() => "#{$this->faker->colorName}",
            'description' => fn() => $this->faker->bs(),
        ];
    }

    public function pm()
    {
        return $this->state(['type' => Channel::TYPES['pm']]);
    }

    public function private()
    {
        return $this->state(['type' => Channel::TYPES['private']]);
    }

    public function public()
    {
        return $this->state(['type' => Channel::TYPES['public']]);
    }

    public function tourney()
    {
        $match = LegacyMatch::factory()->tourney()->create();

        return $this->state([
            'name' => "#mp_{$match->getKey()}",
            'type' => Channel::TYPES['temporary'],
        ]);
    }
}
