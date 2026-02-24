// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import mapperGroup from 'beatmap-discussions/mapper-group';
import SelectOptions from 'components/select-options';
import BeatmapsetDiscussionsStore from 'interfaces/beatmapset-discussions-store';
import UserJson from 'interfaces/user-json';
import { action, computed, makeObservable } from 'mobx';
import { observer } from 'mobx-react';
import { usernameSortAscending } from 'models/user';
import * as React from 'react';
import { makeUrl, parseUrl } from 'utils/beatmapset-discussion-helper';
import { groupColour } from 'utils/css';
import { trans } from 'utils/lang';
import DiscussionsState from './discussions-state';

const allUsers = Object.freeze({
  id: null,
  text: trans('beatmap_discussions.user_filter.everyone'),
});

const noSelection = Object.freeze({
  id: null,
  text: trans('beatmap_discussions.user_filter.label'),
});

interface Option {
  groups: UserJson['groups'];
  id: UserJson['id'] | null;
  text: UserJson['username'];
}

interface Props {
  discussionsState: DiscussionsState;
  store: BeatmapsetDiscussionsStore;
}

function mapUserProperties(user: UserJson): Option {
  return {
    groups: user.groups,
    id: user.id,
    text: user.username,
  };
}

@observer
export class UserFilter extends React.Component<Props> {
  private get ownerId() {
    return this.props.discussionsState.beatmapset.user_id;
  }

  @computed
  private get selected() {
    return this.props.discussionsState.selectedUser != null
      ? mapUserProperties(this.props.discussionsState.selectedUser)
      : noSelection;
  }

  @computed
  private get options() {
    const usersWithDicussions = new Map<number, UserJson>();
    for (const [, discussion] of this.props.store.discussions) {
      if (discussion.message_type === 'hype') continue;

      const user = this.props.store.users.get(discussion.user_id);
      if (user != null && !usersWithDicussions.has(user.id)) {
        usersWithDicussions.set(user.id, user);
      }
    }

    return [
      allUsers,
      ...[...usersWithDicussions.values()]
        .sort(usernameSortAscending)
        .map(mapUserProperties),
    ];
  }

  constructor(props: Props) {
    super(props);
    makeObservable(this);
  }

  render() {
    return (
      <SelectOptions
        href={this.href}
        modifiers='beatmap-discussions-user-filter'
        onChange={this.handleChange}
        options={this.options}
        renderOption={this.renderOption}
        selected={this.selected}
      />
    );
  }

  private getGroup(option: Option) {
    if (this.isOwner(option)) return mapperGroup;

    return option.groups?.[0];
  }

  @action
  private readonly handleChange = (option: Option) => {
    this.props.discussionsState.selectedUserId = option.id;
  };

  private readonly href = (option: Option) => {
    const urlOptions = parseUrl();
    // means it doesn't work on non-beatmapset discussion paths
    if (urlOptions == null) return '';

    urlOptions.user = option.id ?? undefined;

    return makeUrl(urlOptions);
  };

  private isOwner(user?: Option) {
    return user != null && user.id === this.ownerId;
  }

  private readonly renderOption = (option: Option) => (
    <span className='u-group-colour' style={groupColour(this.getGroup(option))}>{option.text}</span>
  );
}
