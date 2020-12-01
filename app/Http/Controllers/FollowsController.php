<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Http\Controllers;

use App\Exceptions\ModelNotSavedException;
use App\Models\Comment;
use App\Models\Follow;
use App\Transformers\FollowCommentTransformer;
use Exception;

class FollowsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function destroy()
    {
        $params = $this->getParams();
        foreach (['notifiable_type', 'notifiable_id', 'subtype'] as $field) {
            if (!isset($params[$field])) {
                abort(422, "Missing parameter follow[{$field}]");
            }
        }

        $follow = Follow::where($params)->first();

        if ($follow !== null) {
            $follow->delete();
        }

        return response([], 204);
    }

    public function indexComment()
    {
        $user = auth()->user();

        $followsQuery = Follow::where(['user_id' => $user->getKey(), 'subtype' => 'comment']);
        $follows = (clone $followsQuery)->with('notifiable')->get();

        $recentCommentIds = Comment
            ::selectRaw('MAX(id) latest_comment_id, commentable_type, commentable_id')
            ->whereIn(
                \DB::raw('(commentable_type, commentable_id)'),
                (clone $followsQuery)->selectRaw('notifiable_type, notifiable_id')
            )->groupBy('commentable_type', 'commentable_id')
            ->get()
            ->pluck('latest_comment_id');

        $comments = Comment::whereIn('id', $recentCommentIds)->with('user')->get();

        $commentsMapped = [];

        foreach ($comments as $comment) {
            $commentsMapped[$comment->commentable_type][$comment->commentable_id] = $comment;
        }

        $followsTransformer = new FollowCommentTransformer($commentsMapped);
        $followsJson = json_collection($follows, $followsTransformer, ['commentable_meta', 'latest_comment.user']);

        return ext_view('follows.comment', compact('followsJson'));
    }

    public function store()
    {
        $params = $this->getParams();
        $follow = new Follow($params);

        try {
            $follow->saveOrExplode();
        } catch (Exception $e) {
            if ($e instanceof ModelNotSavedException) {
                return error_popup($e->getMessage());
            }

            if (!is_sql_unique_exception($e)) {
                throw $e;
            }
        }

        return response([], 204);
    }

    private function getParams()
    {
        $params = get_params(request()->all(), 'follow', ['notifiable_type:string', 'notifiable_id:int', 'subtype:string']);
        $params['user_id'] = auth()->user()->getKey();

        return $params;
    }
}
