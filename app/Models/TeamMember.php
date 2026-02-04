<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'user_id';

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visibleUser(): ?User
    {
        $user = $this->user;

        return $user !== null && !$user->isRestricted() ? $user : null;
    }

    public function userOrDeleted(): User
    {
        return $this->visibleUser()
            ?? new DeletedUser(['user_id' => $this->user_id]);
    }
}
