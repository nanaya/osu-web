<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Transformers;

use App\Models\Team;
use League\Fractal\Resource\Collection;

class TeamTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'members',
    ];

    public function includeMembers(Team $team): Collection
    {
        return $this->collection($team->members, new TeamMemberTransformer());
    }

    public function transform(Team $team): array
    {
        return [
            'created_at' => json_time($team->created_at) ?? '2024-01-01T00:00:00+00:00',
            'description' => [
                'html' => $team->descriptionHtml(),
                'raw' => $team->description ?? '',
            ],
            'header' => $team->header()->url(),
            'id' => $team->getKey(),
            'is_open' => $team->is_open,
            'logo' => $team->logo()->url(),
            'name' => $team->name,
            'ruleset_id' => $team->ruleset_id,
            'short_name' => $team->short_name,
        ];
    }
}
