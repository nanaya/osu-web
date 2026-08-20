<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Libraries\User;

class OsuBcryptHasher
{
    private const ROUNDS = 10;

    public static function make(string $value): string
    {
        // When we originally moved to bcrypt (quite a few years ago),
        // we had to migrate everything without waiting for every user to
        // change their passwords, hence the md5 still being there.
        $hash = password_hash(md5($value), PASSWORD_BCRYPT, ['cost' => static::ROUNDS]);

        // To clarify here: the 2y implementation of bcrypt is specific to
        // the crypt_blowfish implementation of bcrypt (that PHP uses).
        // the act of naming this bugfix "2y" was a stupid idea as it is not
        // an updated version of the algorithm at all.
        // 2a and 2y are literally identical; crypt_blowfish just had bugs in 2a

        // anyways, this replacement is because the .NET library this interacts with
        // needs 2a since a string replacement on every connection is needless overhead
        // for a realtime processing server and only lowers max connections.
        return str_replace('$2y$', '$2a$', $hash);
    }

    public static function check($value, $hashedValue): bool
    {
        return password_verify(md5($value), $hashedValue);
    }
}
