<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Hashing;

use App\Libraries\User\OsuBcryptHasher;
use PHPUnit\Framework\TestCase;

class OsuBcryptHasherTest extends TestCase
{
    public function testBasicHashing()
    {
        $value = OsuBcryptHasher::make('password');
        $this->assertNotSame('password', $value);
        $this->assertNotSame(md5('password'), $value);

        $this->assertTrue(OsuBcryptHasher::check('password', $value));
    }
}
