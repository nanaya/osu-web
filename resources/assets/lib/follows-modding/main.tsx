// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import FollowsSubtypes from 'follows-subtypes';
import HeaderV4 from 'header-v4';
import homeLinks from 'home-links';
import FollowModdingJson from 'interfaces/follow-modding-json';
import UserJson from 'interfaces/user-json';
import { route } from 'laroute';
import * as React from 'react';
import FollowToggle from 'follow-toggle';

interface Props {
  follows: FollowModdingJson[];
  user: UserJson;
}

export default class Main extends React.PureComponent<Props> {
  static defaultProps = {
    user: currentUser,
  };

  render() {
    return (
      <div className='osu-layout osu-layout--full'>
        <HeaderV4
          backgroundImage={this.props.user.cover?.url}
          links={homeLinks('follows@index')}
          theme='settings'
        />

        <div className='osu-page osu-page--generic osu-page--full'>
          <FollowsSubtypes currentSubtype='modding' />

          <table className='follows-table'>
            <thead>
              <tr className='follows-table__row follows-table__row--header'>
                <th className='follows-table__data'>{osu.trans('follows.modding.table.user')}</th>
                <th className='follows-table__data'>{osu.trans('follows.modding.table.latest_beatmapset')}</th>
                <th className='follows-table__data' />
              </tr>
            </thead>
            <tbody>
              {this.props.follows.map(this.renderItem)}
            </tbody>
          </table>
        </div>
      </div>
    );
  }

  private renderItem = (follow: FollowModdingJson) => {
    const beatmapset = follow.latest_beatmapset;

    return (
      <tr key={follow.notifiable_id} className='follows-table__row'>
        <td className='follows-table__data'>
          <a href={route('users.show', { user: follow.user.id })}>
            <img className='xxxx' src={follow.user.avatar_url} />
            {follow.user.username}
          </a>
        </td>

        <td className='follows-table__data'>
          {
            beatmapset != null
            ? (
              <a href={route('beatmapsets.show', { beatmapset: beatmapset.id })}>
                <img className='yyyy' src={beatmapset.covers.card} />
                {beatmapset.title}
                <span className='zzzz'>
                  {osu.trans(`beatmapsets.show.status.${beatmapset.status}`)}
                </span>
              </a>
            ) : osu.trans('follows.modding.table.latest_beatmapset_empty')
          }
        </td>

        <td className='follows-table__data'>
          <FollowToggle follow={follow} />
        </td>
      </tr>
    );
  }
}
