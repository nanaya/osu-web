// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { route } from 'laroute';
import * as React from 'react';
import { classWithModifiers } from 'utils/css';

interface Props {
  currentType: string;
}

export default function FollowsTypes(props: Props) {
  return (
    <div className='page-tabs page-tabs--follows'>
      {['comment', 'forum_topic', 'modding'].map((t) => (
        <a
          className={classWithModifiers('page-tabs__tab', { active: t === props.currentType })}
          href={route('follows.index', { type: t })}
          key={t}
        >
          {osu.trans(`follows.${t}.title`)}
        </a>
      ))}
    </div>
  );
}
