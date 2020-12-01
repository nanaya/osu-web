// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import HeaderV4 from 'header-v4';
import FollowCommentJson from 'interfaces/follow-comment-json';
import UserJson from 'interfaces/user-json';
import { route } from 'laroute';
import * as React from 'react';
import { StringWithComponent } from 'string-with-component';
import TimeWithTooltip from 'time-with-tooltip';
import FollowToggle from './follow-toggle';

interface Props {
  follows: FollowCommentJson[];
  user: UserJson;
}

export default class Main extends React.PureComponent<Props> {
  static defaultProps = {
    user: currentUser,
  };

  static readonly links = [
    { title: osu.trans('home.user.title'), url: route('home') },
    { title: osu.trans('friends.title_compact'), url: route('friends.index') },
    { title: osu.trans('follows.comment.title_compact'), url: route('follows.comment'), active: true },
    { title: osu.trans('forum.topic_watches.index.title_compact'), url: route('forum.topic-watches.index') },
    { title: osu.trans('beatmapset_watches.index.title_compact'), url: route('beatmapsets.watches.index') },
    { title: osu.trans('accounts.edit.title_compact'), url: route('account.edit') },
  ];

  render() {
    return (
      <div className='osu-layout osu-layout--full'>
        <HeaderV4
          backgroundImage={this.props.user.cover?.url}
          links={Main.links}
          theme='settings'
        />

        <div className='osu-page osu-page--generic'>
          <table className='follows-table'>
            <thead>
              <tr className='follows-table__row follows-table__row--header'>
                <th className='follows-table__data' />
                <th className='follows-table__data'>{osu.trans('follows.comment.table.title')}</th>
                <th className='follows-table__data'>{osu.trans('follows.comment.table.latest_comment')}</th>
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

  private renderItem = (follow: FollowCommentJson) => {
    const key = `${follow.notifiable_type}:${follow.notifiable_id}`;

    return (
      <tr key={key} className='follows-table__row'>
        <td className='follows-table__data'>
          <div className='type-badge'>
            {osu.trans(`comments.commentable_name.${follow.notifiable_type}`)}
          </div>
        </td>

        <td className='follows-table__data'>
          <a href={follow.commentable_meta.url}>
            {follow.commentable_meta.title}
          </a>
        </td>

        <td className='follows-table__data'>
          {follow.latest_comment != null ? (
            <a href={route('comments.show', { comment: follow.latest_comment.id })}>
              <StringWithComponent
                pattern={osu.trans('follows.comment.table.latest_comment_value')}
                mappings={{
                  ':time': <TimeWithTooltip dateTime={follow.latest_comment.created_at} relative={true} />,
                  ':username': follow.latest_comment.user?.username ?? '???',
                }}
              />
            </a>
          ) : osu.trans('follows.comment.table.latest_comment_empty')}
        </td>

        <td className='follows-table__data'>
          <FollowToggle follow={follow} />
        </td>
      </tr>
    );
  }
}
