<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition()
    {
        static $achievementSlugs = [
            'all-packs-anime-1',
            'all-packs-anime-2',
            'all-packs-gamer-1',
            'all-packs-gamer-2',
            'all-packs-rhythm-1',
            'all-packs-rhythm-2',
            'osu-combo-500',
            'osu-combo-750',
            'osu-combo-1000',
            'osu-combo-2000',
        ];

        static $groupings = [
            'Misc Achievements 1',
            'Misc Achievements 2',
        ];

        return [
            'achievement_id' => fn() => $this->faker->unique()->numberBetween(1, 5000),
            'name' => fn() => substr($this->faker->catchPhrase(), 0, 40),
            'description' => fn() => $this->faker->realText(30),
            'quest_instructions' => fn() => $this->faker->realText(30),
            'image' => 'http://s.ppy.sh/images/achievements/gamer2.png',
            'grouping' => fn() => array_rand_val($groupings),
            'slug' => fn() => array_rand_val($achievementSlugs),
            'ordering' => 0,
            'progression' => 0,
        ];
    }
}
