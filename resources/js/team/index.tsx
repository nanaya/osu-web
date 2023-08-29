// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import HeaderV4 from 'components/header-v4';
import { UserCard } from 'components/user-card';
import TeamJson from 'interfaces/team-json';
import { route } from 'laroute';
import { computed, makeObservable } from 'mobx';
import { observer } from 'mobx-react';
import core from 'osu-core-singleton';
import * as React from 'react';
import { urlPresence } from 'utils/css';
import { formatNumber } from 'utils/html';
import { trans } from 'utils/lang';

function fail(message: string): never {
  throw new Error(message);
}

export interface Props {
  team: TeamJson;
}

@observer
export default class Team extends React.Component<Props> {
  private get canEdit() {
    return this.owner.user_id === core.currentUser?.id;
  }

  @computed
  private get owner() {
    return this.props.team.members.find((member) => member.is_owner)
      ?? fail('the team is missing owner');
  }

  @computed
  private get members() {
    return this.props.team.members.filter((member) => member !== this.owner);
  }

  constructor(props: Props) {
    super(props);
    makeObservable(this);
  }

  render() {
    return (
      <>
        <HeaderV4 />

        <div className='osu-page osu-page--generic-compact'>
          <div className='profile-info profile-info--cover profile-info--team'>
            <div
              className='profile-info__bg'
              style={{
                backgroundImage: urlPresence(this.props.team.header),
              }}
            />
            <div className='profile-info__details'>
              <div
                className='profile-info__avatar'
                style={{
                  backgroundImage: urlPresence(this.props.team.logo),
                }}
              />
              <div className='profile-info__info'>
                <h1 className='profile-info__name'>
                  {this.props.team.name}
                </h1>
                <div className='profile-info__flags'>
                  <p className='profile-info__flag'>
                    [{this.props.team.short_name}]
                  </p>
                </div>
              </div>
            </div>
          </div>
          {this.renderBar()}
          <div className='user-profile-pages user-profile-pages--no-tabs'>
            <div className='page-extra'>
              <div className='team-summary'>
                <div>
                  <h2 className='title title--page-extra-small title--page-extra-small-top'>
                    {trans('teams.show.sections.members')}
                  </h2>
                  <div className='team-summary__members'>
                    {this.renderOwner()}
                    {this.renderMembers()}
                  </div>
                </div>
                <div className='team-summary__separator' />
                <div>
                  <div dangerouslySetInnerHTML={{ __html: this.props.team.description.html }} />
                </div>
              </div>
            </div>
          </div>
        </div>
      </>
    );
  }

  private renderBar() {
    return (
      <div className='profile-detail-bar'>
        {this.canEdit &&
          <a
            className='user-action-button user-action-button--profile-page'
            href={route('teams.edit', { team: this.props.team.id })}
          >
            {trans('teams.show.bar.settings')}
          </a>
        }

        <a
          className='user-action-button user-action-button--profile-page'
          href={route('teams.edit', { team: this.props.team.id })}
        >
          {trans('teams.show.bar.settings')}
        </a>
      </div>
    );
  }

  private renderMembers() {
    if (this.members.length === 0) {
      return null;
    }

    return (
      <div className='team-members'>
        <div className='team-members__meta'>
          <span>
            {trans('teams.show.members.members')}
          </span>
          <span>
            {formatNumber(this.members.length)}
          </span>
        </div>
        {this.members.map((m) => (
          <UserCard key={m.id} user={m.user} />
        ))}
      </div>
    );
  }

  private renderOwner() {
    return (
      <div className='team-members team-members--owner'>
        <div className='team-members__meta'>
          {trans('teams.show.members.owner')}
        </div>
        <UserCard user={this.owner.user} />
      </div>
    );
  }
}
