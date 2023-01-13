// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BigButton from 'components/big-button';
import UserListPopup, { createTooltip } from 'components/user-list-popup';
import { route } from 'laroute';
import { action, autorun, computed, makeObservable, observable } from 'mobx';
import { disposeOnUnmount, observer } from 'mobx-react';
import core from 'osu-core-singleton';
import * as React from 'react';
import { renderToStaticMarkup } from 'react-dom/server';
import { toggleFavourite } from 'utils/beatmapset-helper';
import { formatNumber } from 'utils/html';
import { trans } from 'utils/lang';
import { beatmapDownloadDirect } from 'utils/url';
import BeatmapsetMenu from './beatmapset-menu';
import Controller from './controller';

const favouritesToShow = 50;

interface Props {
  controller: Controller;
}

interface DownloadButtonProps {
  bottomTextKey?: string;
  href: string;
  icon?: string;
  osuDirect?: boolean;
  topTextKey?: string;
}

const DownloadButton = ({
  bottomTextKey,
  href,
  topTextKey = '_',
}: DownloadButtonProps) => (
  <BigButton
    href={href}
    modifiers='beatmapset-toolbar'
    props={{
      'data-turbolinks': false,
    }}
    text={{
      bottom: bottomTextKey && trans(`beatmapsets.show.details.download.${bottomTextKey}`),
      top: trans(`beatmapsets.show.details.download.${topTextKey}`),
    }}
  />
);

@observer
export default class Toolbar extends React.Component<Props> {
  private readonly favouriteButtonRef = React.createRef<HTMLButtonElement>();
  @observable private hoveredFavouriteButton = false;

  @computed
  private get favouritePopup() {
    return renderToStaticMarkup(
      <UserListPopup count={this.props.controller.beatmapset.favourite_count} users={this.filteredFavourites} />,
    );
  }

  @computed
  private get filteredFavourites() {
    let ret = this.props.controller.beatmapset.recent_favourites;

    const user = core.currentUser;
    if (user != null) {
      ret = ret.filter((f) => f.id !== user.id);

      if (this.props.controller.beatmapset.has_favourited) {
        ret.unshift(user);
      }
    }

    return ret.slice(0, favouritesToShow);
  }

  constructor(props: Props) {
    super(props);

    makeObservable(this);
  }

  componentDidMount() {
    disposeOnUnmount(this, autorun(this.updateFavouritePopup));
  }

  render() {
    return (
      <div className='beatmapset-toolbar'>
        <div className='beatmapset-toolbar__count'>
          <div>
            <div>
              {trans('beatmapsets.show.details.count.total_play')}
            </div>
            <div className='beatmapset-toolbar__count-value'>
              {formatNumber(this.props.controller.beatmapset.play_count)}
            </div>
          </div>
        </div>

        <div className='beatmapset-toolbar__buttons'>
          {this.renderFavouriteButton()}
          {this.renderDownloadButtons()}
          {this.renderLoginButton()}
          {this.renderMenuButton()}
        </div>
      </div>
    );
  }

  @action
  private readonly onEnterFavouriteButton = () => {
    this.hoveredFavouriteButton = true;
  };

  private renderDownloadButtons() {
    if (core.currentUser != null && !this.props.controller.beatmapset.availability?.download_disabled) {
      return (
        <>
          {this.props.controller.beatmapset.video ? (
            <>
              <DownloadButton
                bottomTextKey='video'
                href={route('beatmapsets.download', { beatmapset: this.props.controller.beatmapset.id })}
              />
              <DownloadButton
                bottomTextKey='no-video'
                href={route('beatmapsets.download', { beatmapset: this.props.controller.beatmapset.id, noVideo: 1 })}
              />
            </>
          ) : (
            <DownloadButton
              href={route('beatmapsets.download', { beatmapset: this.props.controller.beatmapset.id })}
            />
          )}

          <DownloadButton
            href={core.currentUser.is_supporter
              ? beatmapDownloadDirect(this.props.controller.currentBeatmap.id)
              : route('support-the-game')
            }
            osuDirect
            topTextKey='direct'
          />
        </>
      );
    }
  }

  private renderFavouriteButton() {
    const icon = this.props.controller.beatmapset.has_favourited
      ? 'fas fa-heart'
      : 'far fa-heart';

    return (
      <button
        ref={this.favouriteButtonRef}
        className='btn-osu-big btn-osu-big--beatmapset-favourite btn-osu-big--pink'
        onClick={this.toggleFavourite}
        onMouseOver={this.onEnterFavouriteButton}
        onTouchStart={this.onEnterFavouriteButton}
      >
        <span className={icon} />
        {' '}
        {formatNumber(this.props.controller.beatmapset.favourite_count)}
      </button>
    );
  }

  private renderLoginButton() {
    if (core.currentUser == null) {
      return (
        <BigButton
          extraClasses={['js-user-link']}
          modifiers='beatmapset-toolbar'
          text={{
            bottom: trans('beatmapsets.show.details.login_required.bottom'),
            top: trans('beatmapsets.show.details.login_required.top'),
          }}
        />
      );
    }
  }

  private renderMenuButton() {
    if (core.currentUser != null && core.currentUser.id !== this.props.controller.beatmapset.user_id) {
      return (
        <div className='beatmapset-toolbar__menu'>
          <div className='btn-circle btn-circle--page-toggle'>
            <BeatmapsetMenu beatmapset={this.props.controller.beatmapset} />
          </div>
        </div>
      );
    }
  }

  private toggleFavourite = () => {
    toggleFavourite(this.props.controller.beatmapset);
  };

  private readonly updateFavouritePopup = () => {
    if (!this.hoveredFavouriteButton) {
      return;
    }

    const target = this.favouriteButtonRef.current;

    if (target == null) {
      throw new Error('favourite button is missing');
    }

    if (this.filteredFavourites.length < 1) {
      if (target._tooltip === '1') {
        target._tooltip = '';
        $(target).qtip('destroy', true);
      }

      return;
    }

    createTooltip(target, 'top center', '');
    $(target).qtip('set', { 'content.text': this.favouritePopup });
  };
}
