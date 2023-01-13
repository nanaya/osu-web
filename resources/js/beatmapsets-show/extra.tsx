// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import Bar from 'components/bar';
import { computed, makeObservable } from 'mobx';
import { observer } from 'mobx-react';
import * as React from 'react';
import { formatNumber } from 'utils/html';
import { trans } from 'utils/lang';
import Controller from './controller';

interface Props {
  controller: Controller;
}

@observer
export default class Extra extends React.PureComponent<Props> {
  private get beatmap() {
    return this.props.controller.currentBeatmap;
  }

  private get beatmapset() {
    return this.props.controller.beatmapset;
  }

  @computed
  private get userRating() {
    return this.beatmapset.ratings.slice(1).reduce(
      (result, count, rating) => {
        result[rating < 5 ? 'negative' : 'positive'] += count;
        return result;
      },
      { negative: 0, positive: 0 },
    );
  }

  @computed
  private get successRate() {
    if (this.beatmap.playcount === 0) {
      return 0;
    }

    return this.beatmap.passcount / this.beatmap.playcount;
  }

  constructor(props: Props) {
    super(props);
    makeObservable(this);
  }

  render() {
    return (
      <div className='beatmapset-extra'>
        <div className='beatmapset-extra__item'>
          {trans('beatmapsets.show.info.success-rate')}
        </div>
        <div className='beatmapset-extra__item beatmapset-extra__item--value beatmapset-extra__item--success-rate'>
          <span>
            {formatNumber(this.beatmap.passcount)}
            {' / '}
            {formatNumber(this.beatmap.playcount)}
          </span>
          <span>
            {formatNumber(this.successRate, 2, { style: 'percent' })}
          </span>
        </div>
        <div className='beatmapset-extra__item beatmapset-extra__item--bar'>
          <Bar
            current={this.successRate}
            modifiers='beatmapset-extra'
            total={1}
          />
        </div>

        <div className='beatmapset-extra__item'>
          {trans('beatmapsets.show.stats.user-rating')}
        </div>
        <div className='beatmapset-extra__item beatmapset-extra__item--value beatmapset-extra__item--user-rating'>
          <div>{this.userRating.negative}</div>
          <div>{this.userRating.positive}</div>
        </div>
        <div className='beatmapset-extra__item beatmapset-extra__item--bar'>
          <Bar
            current={this.userRating.positive}
            modifiers={['beatmapset-extra', 'beatmapset-extra-rating']}
            total={this.userRating.positive + this.userRating.negative}
          />
        </div>

        {this.beatmapset.is_scoreable && (
          <>
            <div className='beatmapset-extra__item'>
              {trans('beatmapsets.show.stats.rating-spread')}
            </div>
            {this.renderRatingChart()}
          </>
        )}
      </div>
    );
  }

  private renderRatingChart() {
    const ratings = this.beatmapset.ratings.slice(1);
    const maxRating = Math.max(...ratings, 1);

    return (
      <div className='beatmapset-extra__item beatmapset-extra__item--chart'>
        {ratings.map((rating, idx) => (
          <div
            key={idx}
            className='beatmapset-extra__chart-bar'
            style={{
              '--background-position': `${idx * 10}%`,
              '--bar-height': `${100 * rating / maxRating}%`,
            } as React.CSSProperties}
          />
        ))}
      </div>
    );
  }
}
