// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import UserJson from './user-json';

export default interface TeamMemberJson {
  id: number;
  is_owner: boolean;
  user: UserJson;
  user_id: number;
}
