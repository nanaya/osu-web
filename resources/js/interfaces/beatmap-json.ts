// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BeatmapOwnerJson from './beatmap-owner-json';
import BeatmapsetJson from './beatmapset-json';
import Ruleset from './ruleset';
import UserJson from './user-json';

interface BeatmapFailTimesArray {
  exit: number[];
  fail: number[];
}

interface BeatmapJsonAvailableIncludes {
  beatmapset: BeatmapsetJson | null;
  checksum: string | null;
  failtimes: BeatmapFailTimesArray;
  max_combo: number;
  owners: BeatmapOwnerJson[];
  top_tag_ids: { count: number; tag_id: number }[];
  user: UserJson;
}

interface BeatmapJsonDefaultAttributes {
  beatmapset_id: number;
  difficulty_rating: number;
  id: number;
  mode: Ruleset;
  status: string;
  total_length: number;
  user_id: number;
  version: string;
}

type BeatmapJson = BeatmapJsonDefaultAttributes & Partial<BeatmapJsonAvailableIncludes>;

export default BeatmapJson;

export function deletedBeatmap(mode: Ruleset): BeatmapJson {
  return {
    beatmapset_id: 0,
    difficulty_rating: 0,
    id: 0,
    mode,
    status: '',
    total_length: 0,
    user_id: 0,
    version: '',
  };
}
