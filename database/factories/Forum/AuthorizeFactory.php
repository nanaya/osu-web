<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\Forum;

use App\Models\Forum\AuthOption;
use App\Models\Forum\Authorize;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorizeFactory extends Factory
{
    protected $model = Authorize::class;

    public function definition()
    {
        return [];
    }

    public function post()
    {
        return $this->state([
            'auth_option_id' => fn() => (
                AuthOption::where('auth_option', 'f_post')->first()
                ?? AuthOption::factory()->post()->create()
            ),
            'auth_setting' => 1,
        ]);
    }

    public function reply()
    {
        return $this->state([
            'auth_option_id' => fn() => (
                AuthOption::where('auth_option', 'f_reply')->first()
                ?? AuthOption::factory()->reply()->create()
            ),
            'auth_setting' => 1,
        ]);
    }
}
