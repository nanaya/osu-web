// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { arrayHas } from 'utils/contains';

export const gameModes = ['osu', 'taiko', 'fruits', 'mania'] as const;

export function ensureGameMode(maybeMode: string) {
  return arrayHas(gameModes, maybeMode)
    ? maybeMode
    : undefined;
}

type GameMode = typeof gameModes[number];

export default GameMode;
