<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OsuAuthorize extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'OsuAuthorize';
    }
}
