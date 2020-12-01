// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { CommentableMetaJson, CommentJson } from 'interfaces/comment-json';

export default interface FollowCommentJson {
  commentable_meta: CommentableMetaJson;
  latest_comment: CommentJson | null;
  notifiable_id: number;
  notifiable_type: string;
  subtype: string;
}
