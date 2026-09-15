// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { RulesetId } from 'interfaces/ruleset';
import * as React from 'react';
import { classWithModifiers } from 'utils/css';

interface Props {
  provisional: boolean;
  rank: number;
  rulesetId: RulesetId;
  tier: string;
}

export default function MatchmakingTierBadge(props: Props) {
  const className = classWithModifiers(
    'matchmaking-tier-badge',
    [...props.tier.split(' '), props.rulesetId].join('-'),
    { provisional: props.provisional },
  );

  return (
    <div className={className}>
      {props.tier === 'Lustrous' &&
        <div className='matchmaking-tier-badge__lustrous-rank-container'>
          <span className='matchmaking-tier-badge__lustrous-rank'>
            {props.rank}
          </span>
        </div>
      }
    </div>
  );
}
