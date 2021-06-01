<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories;

use App\Models\UserRelation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRelationFactory extends Factory
{
    protected $model = UserRelation::class;

    public function block()
    {
        return $this->state(['foe' => true]);
    }

    public function definition()
    {
        return [];
    }

    public function friend()
    {
        return $this->state(['friend' => true]);
    }
}
