<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Singletons;

use App\Models\UserCoverPreset;
use App\Traits\Memoizes;
use Illuminate\Support\Collection;

class UserCoverPresets
{
    use Memoizes;

    public function forUser(int $userId): ?UserCoverPreset
    {
        $all = $this->all();

        return $all[$userId % count($all)] ?? null;
    }

    public function find(int $id): ?UserCoverPreset
    {
        return $this->memoize(
            __FUNCTION__,
            fn () => $this->all()->keyBy('id'),
        )[$id] ?? null;
    }

    private function all(): Collection
    {
        return $this->memoize(__FUNCTION__, fn () => UserCoverPreset::active()->get());
    }
}
