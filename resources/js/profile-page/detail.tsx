// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import ProfileTournamentBanner from 'components/profile-tournament-banner';
import { observer } from 'mobx-react';
import Badges from 'profile-page/badges';
import Cover from 'profile-page/cover';
import DetailBar from 'profile-page/detail-bar';
import * as React from 'react';
import Controller from './controller';
import DetailNumbers from './detail-numbers';
import Links from './links';
import ProfileEditButton from './profile-edit-button';

interface Props {
  controller: Controller;
}

@observer
export default class Detail extends React.Component<Props> {
  render() {
    const user = this.props.controller.state.user;

    return (
      <>
        <Cover
          coverUrl={this.props.controller.displayCoverUrl}
          currentMode={this.props.controller.currentMode}
          editor={<ProfileEditButton controller={this.props.controller} />}
          isUpdatingCover={this.props.controller.isUpdatingCover}
          user={user}
        />

        {user.active_tournament_banners.map((banner) => (
          <ProfileTournamentBanner key={banner.id} banner={banner} />
        ))}

        <Badges badges={user.badges} />

        <DetailNumbers user={user} />

        <DetailBar user={user} />

        <Links user={user} />
      </>
    );
  }
}
