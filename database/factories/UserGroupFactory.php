<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserGroupFactory extends Factory
{
    protected $model = UserGroup::class;

    public function definition()
    {
        return [
            'group_leader' => 0,
            'user_pending' => 0,
        ];
    }
}
