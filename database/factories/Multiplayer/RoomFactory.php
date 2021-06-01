<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Multiplayer;

use App\Models\Chat\Channel;
use App\Models\Multiplayer\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function configure()
    {
        return $this->afterCreating(function (Room $room) {
            $channel = Channel::createMultiplayer($room);

            $room->update(['channel_id' => $channel->getKey()]);
        });
    }

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => fn() => $this->faker->realText(20),
            'starts_at' => fn() => now()->subHour(1),
            'ends_at' => fn() => now()->addHour(1),
        ];
    }

    public function ended()
    {
        return $this->state(['ends_at' => now()->subMinute(1)]);
    }
}
