// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BeatmapList from 'beatmap-discussions/beatmap-list';
import BeatmapsetBadge from 'components/beatmapset-badge';
import StringWithComponent from 'components/string-with-component';
import { UserLink } from 'components/user-link';
import BeatmapJson from 'interfaces/beatmap-json';
import { route } from 'laroute';
import { observer } from 'mobx-react';
import * as React from 'react';
import { getArtist, getTitle } from 'utils/beatmap-helper';
import { generate as generateHash } from 'utils/beatmapset-page-hash';
import { trans } from 'utils/lang';
import BeatmapPicker from './beatmap-picker';
import Controller from './controller';

interface Props {
  controller: Controller;
}

@observer
export default class Header extends React.Component<Props> {
  private get controller() {
    return this.props.controller;
  }

  render() {
    return (
      <div className='beatmapset-header'>
        <div className='beatmapset-header__status'>
          <div
            className='beatmapset-status beatmapset-status--header'
            style={{
              '--bg-hsl': `var(--beatmapset-${this.controller.currentBeatmap.status}-bg-hsl)`,
              '--colour': `var(--beatmapset-${this.controller.currentBeatmap.status}-colour)`,
            } as React.CSSProperties}
          >
            {trans(`beatmapsets.show.status.${this.controller.currentBeatmap.status}`)}
          </div>

          <BeatmapsetBadge
            beatmapset={this.controller.beatmapset}
            modifiers='header'
            type='spotlight'
          />
          <BeatmapsetBadge
            beatmapset={this.controller.beatmapset}
            modifiers='header'
            type='nsfw'
          />
        </div>

        <div className='beatmapset-header__title-container u-ellipsis-overflow'>
          <div className='beatmapset-header__title u-ellipsis-overflow'>
            <a
              className='beatmapset-header__text-link'
              href={route('beatmapsets.index', { q: getTitle(this.controller.beatmapset) })}
            >
              {getTitle(this.controller.beatmapset)}
            </a>
          </div>

          <div className='beatmapset-header__artist u-ellipsis-overflow'>
            <StringWithComponent
              mappings={{
                artist:
                  <a
                    className='beatmapset-header__text-link'
                    href={route('beatmapsets.index', { q: getArtist(this.controller.beatmapset) })}
                  >
                    {getArtist(this.controller.beatmapset)}
                  </a>,
              }}
              pattern={trans('beatmapsets.show.details.by_artist')}
            />
            <BeatmapsetBadge
              beatmapset={this.controller.beatmapset}
              modifiers='inline'
              type='featured_artist'
            />
          </div>
        </div>

        <div className='beatmapset-header__creator'>
          <StringWithComponent
            mappings={{
              creator:
                <UserLink
                  user={{ id: this.controller.beatmapset.user_id, username: this.controller.beatmapset.creator }}
                />,
            }}
            pattern={trans('beatmapsets.show.details.created_by')}
          />
        </div>

        <div className='beatmapset-header__chooser'>
          <div className='beatmapset-header__chooser-list'>
            <BeatmapList
              beatmaps={this.controller.currentBeatmaps}
              beatmapset={this.controller.beatmapset}
              createLink={this.generateBeatmapLink}
              currentBeatmap={this.controller.currentBeatmap}
              large={false}
              modifiers='beatmapset-show'
              onSelectBeatmap={this.onSelectBeatmap}
              users={this.controller.usersById}
            />
          </div>

          <div className='beatmapset-header__chooser-picker'>
            <BeatmapPicker controller={this.controller} />
          </div>
        </div>
      </div>
    );
  }

  private readonly generateBeatmapLink = (beatmap: BeatmapJson) => generateHash({
    beatmap,
    ruleset: this.controller.currentBeatmap.mode,
  });

  private onSelectBeatmap = (beatmapId: number) => {
    const selectedBeatmap = this.controller.currentBeatmaps.find((beatmap) => beatmap.id === beatmapId);

    if (selectedBeatmap == null) {
      throw new Error('invalid beatmapId specified');
    }

    this.controller.setCurrentBeatmap(selectedBeatmap);
  };
}
