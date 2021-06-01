<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\LegacyMatch;

use App\Models\LegacyMatch\Event;
use App\Models\LegacyMatch\Game;
use App\Models\LegacyMatch\LegacyMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'match_id' => LegacyMatch::factory(),
            'user_id' => User::factory(),
            'timestamp' => fn() => now(),
        ];
    }

    public function stateCreate()
    {
        return $this->state([
            'user_id' => null,
            'text' => 'CREATE',
        ]);
    }

    public function disband()
    {
        return $this->state([
            'user_id' => null,
            'text' => 'DISBAND',
        ]);
    }

    public function join()
    {
        return $this->state(['text' => 'JOIN']);
    }

    public function part()
    {
        return $this->state(['text' => 'PART']);
    }

    public function game()
    {
        return $this->state([
            'text' => 'test game',
            'user_id' => null,
            'game_id' => Game::factory()->inProgress(),
        ]);
    }
}
