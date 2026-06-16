// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { observer } from 'mobx-react';
import MedalsCount from 'profile-page/medals-count';
import PlayTime from 'profile-page/play-time';
import Pp from 'profile-page/pp';
import Rank from 'profile-page/rank';
import RankChart from 'profile-page/rank-chart';
import RankCount from 'profile-page/rank-count';
import Stats from 'profile-page/stats';
import * as React from 'react';
import { trans } from 'utils/lang';
import Controller from './controller';
import DailyChallenge from './daily-challenge';
import Matchmaking from './matchmaking';

interface Props {
  user: Controller['state']['user'];
}

@observer
export default class DetailNumbers extends React.PureComponent<Props> {
  render() {
    const user = this.props.user;

    if (user.is_bot) return null;

    return (
      <div className='profile-detail'>
        <div className='profile-detail__stats'>
          <div>
            <div className='profile-detail__chart-numbers profile-detail__chart-numbers--top'>
              <div className='profile-detail__values'>
                <Rank highest={user.rank_highest} stats={user.statistics} type='global' />
                <Rank stats={user.statistics} type='country' />
              </div>
              <div className='profile-detail__values'>
                <Matchmaking allStats={user.matchmaking_stats} />
                <DailyChallenge stats={user.daily_challenge_user_stats} />
              </div>
            </div>

            <div className='profile-detail__chart'>
              {user.statistics.is_ranked ? (
                <RankChart rankHistory={user.rank_history} stats={user.statistics} />
              ) : (
                <div className='profile-detail__empty-chart'>{trans('users.show.extra.unranked')}</div>
              )}
            </div>
            <div className='profile-detail__chart-numbers'>
              <div className='profile-detail__values profile-detail__values--grid'>
                <MedalsCount userAchievements={user.user_achievements} />
                <Pp stats={user.statistics} />
                <PlayTime stats={user.statistics} />
              </div>
              <div className='profile-detail__values'>
                <RankCount stats={user.statistics} />
              </div>
            </div>
          </div>

          <div className='profile-detail__separator' />

          <Stats stats={user.statistics} />
        </div>
      </div>
    );
  }
}
