<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Database\Factories\OAuth;

use App\Models\OAuth\Token;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Passport\RefreshToken;

class RefreshTokenFactory extends Factory
{
    protected $model = RefreshToken::class;

    public function definition()
    {
        return [
            'id' => str_random(40),
            'access_token_id' => Token::factory(),
            'revoked' => false,
            'expires_at' => fn() => now()->addDay(),
        ];
    }
}
