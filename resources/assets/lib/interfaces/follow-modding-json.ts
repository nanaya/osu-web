// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import FollowJson from 'interfaces/follow-json';
import UserJson from 'interfaces/user-json';
import { BeatmapsetJson } from 'beatmapsets/beatmapset-json';

export default interface FollowModdingJson extends FollowJson {
  user: UserJson;
  latest_beatmapset?: BeatmapsetJson;
}
