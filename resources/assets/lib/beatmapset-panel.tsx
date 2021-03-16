// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { BeatmapsetJson, BeatmapsetStatus } from 'beatmapsets/beatmapset-json';
import { CircularProgress } from 'circular-progress';
import { Img2x } from 'img2x';
import BeatmapJson from 'interfaces/beatmap-json';
import { route } from 'laroute';
import { sum, values } from 'lodash';
import { computed } from 'mobx';
import { observer } from 'mobx-react';
import OsuUrlHelper from 'osu-url-helper';
import * as React from 'react';
import { StringWithComponent } from 'string-with-component';
import TimeWithTooltip from 'time-with-tooltip';
import { UserLink } from 'user-link';
import * as BeatmapHelper from 'utils/beatmap-helper';
import { showVisual, toggleFavourite } from 'utils/beatmapset-helper';

interface Props {
  beatmapset: BeatmapsetJson;
}

const displayDateMap: Record<BeatmapsetStatus, 'last_updated' | 'ranked_date'> = {
  approved: 'ranked_date',
  graveyard: 'last_updated',
  loved: 'ranked_date',
  pending: 'last_updated',
  qualified: 'ranked_date',
  ranked: 'ranked_date',
  wip: 'last_updated',
};

@observer
export default class BeatmapsetPanel extends React.Component<Props> {
  @computed
  private get displayDate() {
    const attribute = displayDateMap[this.props.beatmapset.status];

    return this.props.beatmapset[attribute];
  }

  @computed
  private get downloadLink() {
    if (currentUser.id == null) {
      return { title: osu.trans('beatmapsets.show.details.logged-out') };
    }

    if (this.props.beatmapset.availability?.download_disabled) {
      return { title: osu.trans('beatmapsets.availability.disabled') };
    }

    let type = currentUser.user_preferences.beatmapset_download;
    if (type === 'direct' && !currentUser.is_supporter) {
      type = 'all';
    }

    let url: string;
    let titleVariant: string;

    if (type === 'direct') {
        url = OsuUrlHelper.beatmapsetDownloadDirect(this.props.beatmapset.id);
        titleVariant = 'direct';
    } else {
      const params: Record<string, string|number> = {
        beatmapset: this.props.beatmapset.id,
      };

      if (this.props.beatmapset.video) {
        if (type === 'no_video') {
          params.noVideo = 1;
          titleVariant = 'no_video';
        } else {
          titleVariant = 'video';
        }
      } else {
        titleVariant = 'all';
      }

      url = route('beatmapsets.download', params);
    }

    return {
      title: osu.trans(`beatmapsets.panel.download.${titleVariant}`),
      url,
    };
  }

  @computed
  private get favourite() {
    return this.props.beatmapset.has_favourited
      ? {
        icon: 'fas fa-heart',
        toggleTitleVariant: 'unfavourite',
      }
      : {
        icon: 'far fa-heart',
        toggleTitleVariant: 'favourite',
      };
  }

  @computed
  private get nominations() {
    if (this.props.beatmapset.nominations_summary != null) {
      return this.props.beatmapset.nominations_summary;
    }

    if (this.props.beatmapset.nominations != null) {
      if (this.props.beatmapset.nominations.legacy_mode) {
        return this.props.beatmapset.nominations;
      }

      return {
        current: sum(values(this.props.beatmapset.nominations.current)),
        required: sum(values(this.props.beatmapset.nominations.required)),
      };
    }
  }

  @computed
  private get showHypeCounts() {
    return this.props.beatmapset.hype != null;
  }

  @computed
  private get showVisual() {
    return showVisual(this.props.beatmapset);
  }

  @computed
  private get url() {
    return route('beatmapsets.show', { beatmapset: this.props.beatmapset.id});
  }

  render() {
    return (
      <div
        className={`beatmapset-panel ${this.showVisual ? 'js-audio--player' : ''}`}
        data-audio-url={this.props.beatmapset.preview_url}
      >
        <a
          href={this.url}
          className='beatmapset-panel__cover-container'
        >
          <div className='beatmapset-panel__cover beatmapset-panel__cover--default' />
          {this.showVisual && (
            <Img2x
              className='beatmapset-panel__cover'
              onError={this.hideImage}
              src={this.props.beatmapset.covers.card}
            />
          )}
        </a>
        <div className='beatmapset-panel__content'>
          <div className='beatmapset-panel__play-container'>
            <div className='beatmapset-panel__extra-icons'>
              {this.renderVideoIcon()}
              {this.renderStoryboardIcon()}
            </div>
            {this.showVisual && (
              <button
                type='button'
                className='beatmapset-panel__play js-audio--play'
              />
            )}
            <div className='beatmapset-panel__play-progress'>
              <CircularProgress
                current={0}
                max={1}
                theme='beatmapset-panel'
                onlyShowAsWarning={false}
                ignoreProgress={true}
              />
            </div>
          </div>
          <div className='beatmapset-panel__info'>
            <div className='beatmapset-panel__info-row beatmapset-panel__info-row--title'>
              <a
                className='beatmapset-panel__main-link u-ellipsis-overflow'
                href={this.url}
              >
                {BeatmapHelper.getTitle(this.props.beatmapset)}
              </a>
              {this.renderNsfwBadge()}
            </div>
            <div className='beatmapset-panel__info-row beatmapset-panel__info-row--artist'>
              <a
                className='beatmapset-panel__main-link u-ellipsis-overflow'
                href={this.url}
              >
                {osu.trans('beatmapsets.show.details.by_artist', { artist: BeatmapHelper.getArtist(this.props.beatmapset) })}
              </a>
            </div>
            <div className='beatmapset-panel__info-row beatmapset-panel__info-row--mapper'>
              <div className='u-ellipsis-overflow'>
                <StringWithComponent
                  pattern={osu.trans('beatmapsets.show.details.mapped_by')}
                  mappings={{
                    ':mapper': this.renderMapperLink(),
                  }}
                />
              </div>
            </div>

            <div className='beatmapset-panel__info-row beatmapset-panel__info-row--stats'>
              {this.showHypeCounts && this.props.beatmapset.hype != null && this.renderStatsItem({
                icon: 'fas fa-bullhorn',
                title: osu.trans('beatmaps.hype.required_text', {
                  current: osu.formatNumber(this.props.beatmapset.hype.current),
                  required: osu.formatNumber(this.props.beatmapset.hype.required),
                }),
                value: this.props.beatmapset.hype.current,
              })}

              {this.showHypeCounts && this.nominations != null && this.renderStatsItem({
                icon: 'fas fa-thumbs-up',
                title: osu.trans('beatmaps.nominations.required_text', {
                  current: osu.formatNumber(this.nominations.current),
                  required: osu.formatNumber(this.nominations.required),
                }),
                value: this.nominations.current,
              })}

              {this.renderStatsItem({
                icon: 'fas fa-play-circle',
                title: osu.trans('beatmaps.panel.playcount', { count: osu.formatNumber(this.props.beatmapset.play_count) }),
                value: this.props.beatmapset.play_count,
              })}

              {this.renderStatsItem({
                icon: this.favourite.icon,
                title: osu.trans('beatmaps.panel.favourites', { count: osu.formatNumber(this.props.beatmapset.favourite_count) }),
                value: this.props.beatmapset.favourite_count,
              })}

              <div className='beatmapset-panel__stats-item'>
                <span className='beatmapset-panel__stats-item-icon'>
                  <i className='fas fa-fw fa-check-circle' />
                </span>
                <TimeWithTooltip dateTime={this.displayDate} format='L' />
              </div>
            </div>

            <div className='beatmapset-panel__info-row'>
              <div
                className='beatmapset-status beatmapset-status--panel'
                style={{
                  '--bg': `var(--beatmapset-${this.props.beatmapset.status}-bg)`,
                  '--colour': `var(--beatmapset-${this.props.beatmapset.status}-colour)`,
                } as React.CSSProperties}
              >
                {osu.trans(`beatmapsets.show.status.${this.props.beatmapset.status}`)}
              </div>
              <div className='beatmapset-panel__beatmaps-all'>
                {this.renderBeatmapIcons()}
              </div>
            </div>
          </div>

          <div className='beatmapset-panel__menu-container'>
            <div className='beatmapset-panel__menu'>
              <button
                className='beatmapset-panel__menu-item js-login-required--click'
                onClick={this.toggleFavourite}
                title={osu.trans(`beatmapsets.show.details.${this.favourite.toggleTitleVariant}`)}
                type='button'
              >
                <span className={this.favourite.icon} />
              </button>

              <a
                href={route('beatmapsets.discussion', { beatmapset: this.props.beatmapset.id })}
                className='beatmapset-panel__menu-item'
              >
                <span className='fas fa-comment-alt' />
              </a>

              {this.renderDownloadLink()}
            </div>
          </div>
        </div>
      </div>
    );
  }

  private hideImage(e: React.SyntheticEvent<HTMLElement>) {
    // hides img elements that have errored (hides native browser broken-image icons)
    e.currentTarget.style.display = 'none';
  }

  private renderBeatmapIcon(beatmap: BeatmapJson) {
    return (
      <div
        className='beatmapset-panel__beatmap'
        style={{
          '--bg': `var(--diff-${BeatmapHelper.getDiffRating(beatmap.difficulty_rating)})`,
        } as React.CSSProperties}
        key={`beatmap-${beatmap.id}`}
      />
    );
  }

  private renderBeatmapIcons() {
    const groupedBeatmaps = BeatmapHelper.group(this.props.beatmapset.beatmaps ?? []);

    return BeatmapHelper.modes.map((mode) => {
      const beatmaps = groupedBeatmaps[mode];

      if (beatmaps == null) return null;

      return (
        <div className='beatmapset-panel__beatmaps' key={mode}>
          <div className='beatmapset-panel__beatmap-icon'>
            <i className={`fal fa-extra-mode-${mode}`} />
          </div>
          {beatmaps.map(this.renderBeatmapIcon)}
        </div>
      );
    });
  }

  private renderDownloadLink() {
    return (this.downloadLink.url == null)
      ? (
        <span
          title={this.downloadLink.title}
          className='beatmapset-panel__menu-item beatmapset-panel__menu-item--disabled'
        >
          <span className='fas fa-file-download' />
        </span>
      )
      : (
        <a
          href={this.downloadLink.url}
          title={this.downloadLink.title}
          className='beatmapset-panel__menu-item'
          data-turbolinks='false'
        >
          <span className='fas fa-file-download' />
        </a>
      );
  }

  private renderMapperLink() {
    return (
      <UserLink
        key='mapper'
        user={{ id: this.props.beatmapset.user_id, username: this.props.beatmapset.creator }}
        className='beatmapset-panel__mapper-link u-hover'
      />
    );
  }

  private renderNsfwBadge() {
    if (!this.props.beatmapset.nsfw) return null;

    return (
      <span className='nsfw-badge nsfw-badge--panel'>
        {osu.trans('beatmapsets.nsfw_badge.label')}
      </span>
    );
  }

  private renderStatsItem({ icon, title, value }: { icon: string, title: string, value: number }) {
    return (
      <div
        className='beatmapset-panel__stats-item u-hover'
        title={title}
      >
        <span className='beatmapset-panel__stats-item-icon'>
          <i className={icon} />
        </span>
        <span>{osu.formatNumberSuffixed(value, 0)}</span>
      </div>
    );
  }

  private renderStoryboardIcon() {
    if (!this.props.beatmapset.storyboard) return null;

    return (
      <div
        className='beatmapset-panel__extra-icon'
        title={osu.trans('beatmapsets.show.info.storyboard')}
      >
        <i className='fas fa-image' />
      </div>
    );
  }

  private renderVideoIcon() {
    if (!this.props.beatmapset.video) return null;

    return (
      <div
        className='beatmapset-panel__extra-icon'
        title={osu.trans('beatmapsets.show.info.video')}
      >
        <i className='fas fa-film' />
      </div>
    );
  }

  private toggleFavourite = () =>
    toggleFavourite(this.props.beatmapset)
}
