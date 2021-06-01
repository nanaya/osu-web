<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Multiplayer;

use App\Models\Multiplayer\PlaylistItem;
use App\Models\Multiplayer\Score;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScoreFactory extends Factory
{
    protected $model = Score::class;

    public function completed()
    {
        return $this->state(['ended_at' => now()]);
    }

    public function definition()
    {
        return [
            'playlist_item_id' => PlaylistItem::factory(),
            'beatmap_id' => fn(array $attributes) => PlaylistItem::find($attributes['playlist_item_id'])->beatmap_id,
            'room_id' => fn(array $attributes) => PlaylistItem::find($attributes['playlist_item_id'])->room_id,
            'user_id' => User::factory(),
            'total_score' => 1,
            'started_at' => fn() => now()->subMinutes(5),
            'accuracy' => 0.5,
            'pp' => 1,
        ];
    }

    public function failed()
    {
        return $this->completed()->state(['passed' => false]);
    }

    public function passed()
    {
        return $this->completed()->state(['passed' => true]);
    }

    public function scoreless()
    {
        return $this->state(['total_score' => 0]);
    }
}
