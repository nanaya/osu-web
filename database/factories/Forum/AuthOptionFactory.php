<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Forum;

use App\Models\Forum\AuthOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthOptionFactory extends Factory
{
    protected $model = AuthOption::class;

    public function definition()
    {
        return [];
    }

    public function post()
    {
        return $this->state(['auth_option' => 'f_post']);
    }

    public function reply()
    {
        return $this->state(['auth_option' => 'f_reply']);
    }
}
