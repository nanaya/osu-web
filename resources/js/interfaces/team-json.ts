// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import TeamMemberJson from './team-member-json';

export default interface TeamJson {
  created_at: string;
  description: {
    html: string;
    raw: string;
  };
  header: string | null;
  id: number;
  is_open: boolean;
  logo: string | null;
  members: TeamMemberJson[];
  name: string;
  ruleset_id: number;
  short_name: string;
}
