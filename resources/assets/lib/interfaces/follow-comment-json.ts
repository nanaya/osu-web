// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import FollowJson from 'interfaces/follow-json';
import { CommentableMetaJson, CommentJson } from 'interfaces/comment-json';

export default interface FollowCommentJson extends FollowJson {
  commentable_meta: CommentableMetaJson;
  latest_comment?: CommentJson;
}
