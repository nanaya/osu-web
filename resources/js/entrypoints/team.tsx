// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import core from 'osu-core-singleton';
import * as React from 'react';
import Team, { Props } from 'team';

core.reactTurbolinks.register('team', (container: HTMLElement) => {
  const props = JSON.parse(container.dataset.props ?? '') as Props;

  return (
    <Team {...props} />
  );
});
