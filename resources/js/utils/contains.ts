// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

export function arrayHas<T>(arr: readonly T[], val: unknown): val is T {
  return (arr as unknown[]).includes(val);
}

export function setHas<T>(set: Set<T>, val: unknown): val is T {
  return (set as Set<unknown>).has(val);
}
