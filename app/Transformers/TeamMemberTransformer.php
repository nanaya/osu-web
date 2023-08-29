<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Transformers;

use App\Models\TeamMember;
use League\Fractal\Resource\Item;

class TeamMemberTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'user',
    ];

    public function includeUser(TeamMember $member): Item
    {
        return $this->item($member->user, new UserCompactTransformer());
    }

    public function transform(TeamMember $member): array
    {
        return [
            'id' => $member->getKey(),
            'user_id' => $member->user_id,
            'is_owner' => $member->is_owner,
        ];
    }
}
