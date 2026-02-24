// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import SelectOptions from 'components/select-options';
import Ruleset from 'interfaces/ruleset';
import SelectOptionJson from 'interfaces/select-option-json';
import { route } from 'laroute';
import * as React from 'react';
import { Modifiers } from 'utils/css';
import { fail } from 'utils/fail';
import { navigate } from 'utils/turbolinks';
import { updateQueryString } from 'utils/url';

interface PropsBase {
  currentItem: SelectOptionJson;
  items: SelectOptionJson[];
  modifiers?: Modifiers;
}

type Props = PropsBase & ({
  type: 'daily_challenge' | 'download' | 'multiplayer' | 'seasons' | 'spotlight';
} | {
  ruleset: Ruleset;
  type: 'matchmaking';
});

export default class BasicSelectOptions extends React.PureComponent<Props> {
  render() {
    return (
      <SelectOptions
        href={this.href}
        modifiers={this.props.modifiers}
        onChange={this.handleChange}
        options={this.props.items}
        selected={this.props.currentItem}
      />
    );
  }

  private readonly handleChange = (option: SelectOptionJson) => {
    navigate(this.href(option));
  };

  private readonly href = ({ id }: SelectOptionJson) => {
    switch (this.props.type) {
      case 'daily_challenge':
        return route('daily-challenge.show', { daily_challenge: id ?? fail('missing id parameter') });
      case 'download':
        return route('download', { platform: id });
      case 'matchmaking':
        return route('rankings.matchmaking', { mode: this.props.ruleset, pool: id ?? undefined });
      case 'multiplayer':
        return route('multiplayer.rooms.show', { room: id ?? 'latest' });
      case 'seasons':
        return route('seasons.show', { season: id ?? 'latest' });
      case 'spotlight':
        return updateQueryString(null, { spotlight: id?.toString() });
    }
  };
}
