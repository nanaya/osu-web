<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Models;

use App\Libraries\Uploader;
use App\Libraries\User\Cover;

/**
 * @property int $id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class UserCoverPreset extends Model
{
    private Uploader $file;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)->whereNotNull('filename');
    }

    public function file(): Uploader
    {
        return $this->file ??= new Uploader(
            'user-cover-presets',
            $this,
            'filename',
            ['image' => ['maxDimensions' => Cover::CUSTOM_COVER_MAX_DIMENSIONS]],
        );
    }
}
