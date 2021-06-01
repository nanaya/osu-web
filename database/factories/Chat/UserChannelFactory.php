<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Chat;

use App\Models\Chat\Channel;
use App\Models\Chat\UserChannel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserChannelFactory extends Factory
{
    protected $model = UserChannel::class;

    public function definition()
    {
        return [
            'channel_id' => Channel::factory(),
            'user_id' => User::factory(),
        ];
    }
}
