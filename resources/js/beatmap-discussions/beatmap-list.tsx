// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BeatmapListItem from 'components/beatmap-list-item';
import BeatmapExtendedJson from 'interfaces/beatmap-extended-json';
import BeatmapJson from 'interfaces/beatmap-json';
import BeatmapsetExtendedJson from 'interfaces/beatmapset-extended-json';
import UserJson from 'interfaces/user-json';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import { deletedUser } from 'models/user';
import * as React from 'react';
import { blackoutToggle } from 'utils/blackout';
import { classWithModifiers, Modifiers } from 'utils/css';
import { formatNumber, isClickable } from 'utils/html';

interface Props {
  beatmaps: BeatmapExtendedJson[];
  beatmapset: BeatmapsetExtendedJson;
  createLink: (beatmap: BeatmapJson) => string;
  currentBeatmap: BeatmapExtendedJson;
  getCount?: (beatmap: BeatmapExtendedJson) => number | undefined;
  large: boolean;
  modifiers?: Modifiers;
  onSelectBeatmap: (beatmapId: number) => void;
  users: Partial<Record<number | string, UserJson>>;
}

@observer
export default class BeatmapList extends React.Component<Props> {
  static defaultProps = {
    large: true,
    users: {},
  };

  private readonly selectorRef = React.createRef<HTMLDivElement>();
  @observable private showingSelector = false;

  constructor(props: Props) {
    super(props);
    makeObservable(this);
  }

  componentDidMount() {
    document.addEventListener('click', this.onDocumentClick);
    document.addEventListener('turbolinks:before-cache', this.hideSelector);
    this.syncBlackout();
  }

  componentWillUnmount() {
    document.removeEventListener('click', this.onDocumentClick);
    document.removeEventListener('turbolinks:before-cache', this.hideSelector);
  }

  render() {
    return (
      <div className={classWithModifiers('beatmap-list', this.props.modifiers, { selecting: this.showingSelector })}>
        <div className='beatmap-list__body'>
          <div
            ref={this.selectorRef}
            className='beatmap-list__item beatmap-list__item--selected beatmap-list__item--large'
            onClick={this.toggleSelector}
          >
            <div className='beatmap-list__selected-icons'>
              <span className='fas fa-circle u-relative' />
              <span className='fas fa-circle u-relative' />
              <span className='fas fa-circle u-relative' />
              <span className='fas fa-circle u-relative' />
            </div>
            <div className='beatmap-list__selected-list'>
              <BeatmapListItem
                beatmap={this.props.currentBeatmap}
                mapper={null}
                modifiers={{ large: this.props.large }}
              />

              <div className='beatmap-list__item-selector-button'>
                <span className='fas fa-chevron-down' />
              </div>
            </div>
          </div>

          <div className='beatmap-list__selector-container'>
            <div className='beatmap-list__selector'>
              {this.props.beatmaps.map(this.beatmapListItem)}
            </div>
          </div>
        </div>
      </div>
    );
  }

  private readonly beatmapListItem = (beatmap: BeatmapExtendedJson) => {
    const count = this.props.getCount?.(beatmap);

    return (
      <div
        key={beatmap.id}
        className={classWithModifiers('beatmap-list__item', { current: beatmap.id === this.props.currentBeatmap.id })}
        data-id={beatmap.id}
        onClick={this.selectBeatmap}
      >
        <BeatmapListItem
          beatmap={beatmap}
          beatmapUrl={this.props.createLink(beatmap)}
          beatmapset={this.props.beatmapset}
          mapper={this.props.users[beatmap.user_id] ?? deletedUser}
          showNonGuestMapper={false}
        />
        {count != null &&
          <div className='beatmap-list__item-count'>
            {formatNumber(count)}
          </div>
        }
      </div>
    );
  };

  private readonly hideSelector = () => {
    this.setSelector(false);
  };

  private readonly onDocumentClick = (e: MouseEvent) => {
    if (isClickable(e.target)) return;
    if (
      this.selectorRef.current != null
      && e.composedPath().includes(this.selectorRef.current)
    ) return;

    this.hideSelector();
  };

  private readonly selectBeatmap = (e: React.MouseEvent<HTMLElement>) => {
    if (isClickable(e.target)) return;

    const beatmapId = parseInt(e.currentTarget.dataset.id ?? '', 10);
    this.props.onSelectBeatmap(beatmapId);
  };

  @action
  private readonly setSelector = (state: boolean) => {
    this.showingSelector = state;
  };

  private readonly syncBlackout = () => {
    blackoutToggle(this.showingSelector, 0.5);
  };

  private readonly toggleSelector = (e: React.MouseEvent<HTMLElement>) => {
    if (isClickable(e.target)) return;

    this.setSelector(!this.showingSelector);
  };
}
