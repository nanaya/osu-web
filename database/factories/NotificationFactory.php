<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Libraries\MorphMap;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'notifiable_type' => array_rand_val(MorphMap::MAP),
            'notifiable_id' => rand(),
            'name' => array_rand(Notification::NAME_TO_CATEGORY),
            'details' => [],
        ];
    }
}
