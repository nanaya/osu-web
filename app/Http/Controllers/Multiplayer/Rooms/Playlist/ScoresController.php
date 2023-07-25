<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Http\Controllers\Multiplayer\Rooms\Playlist;

use App\Http\Controllers\Controller as BaseController;
use App\Libraries\ClientCheck;
use App\Models\Multiplayer\PlaylistItem;
use App\Models\Multiplayer\PlaylistItemUserHighScore;
use App\Models\Multiplayer\Room;
use App\Models\Solo\Score as SoloScore;
use App\Transformers\Multiplayer\ScoreTransformer;
use App\Transformers\ScoreTransformer as SoloScoreTransformer;

/**
 * @group Multiplayer
 */
class ScoresController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('require-scopes:public', ['only' => ['index']]);
    }

    /**
     * Get Scores
     *
     * Returns a list of scores for specified playlist item.
     *
     * ---
     *
     * ### Response Format
     *
     * Returns [MultiplayerScores](#multiplayerscores) object.
     *
     * @urlParam room integer required Id of the room.
     * @urlParam playlist integer required Id of the playlist item.
     *
     * @queryParam limit Number of scores to be returned.
     * @queryParam sort [MultiplayerScoresSort](#multiplayerscoressort) parameter.
     * @queryParam cursor_string [CursorString](#cursorstring) parameter.
     */
    public function index($roomId, $playlistId)
    {
        $playlist = PlaylistItem::where('room_id', $roomId)->where('id', $playlistId)->firstOrFail();
        $params = request()->all();
        $limit = clamp(get_int($params['limit'] ?? null) ?? 50, 1, 50);
        $cursorHelper = PlaylistItemUserHighScore::makeDbCursorHelper($params['sort'] ?? null);

        [$highScores, $hasMore] = $playlist
            ->highScores()
            ->cursorSort($cursorHelper, cursor_from_params($params))
            ->with(ScoreTransformer::BASE_PRELOAD)
            ->limit($limit)
            ->getWithHasMore();

        $scoresJson = json_collection(
            $highScores->pluck('score'),
            'Multiplayer\Score',
            ScoreTransformer::BASE_INCLUDES
        );
        $total = $playlist->highScores()->count();

        $user = auth()->user();

        if ($user !== null) {
            $userHighScore = $playlist->highScores()->where('user_id', $user->getKey())->first();

            if ($userHighScore !== null) {
                $userScoreJson = json_item($userHighScore->score, 'Multiplayer\Score', ScoreTransformer::BASE_INCLUDES);
            }
        }

        return [
            'params' => ['limit' => $limit, 'sort' => $cursorHelper->getSortName()],
            'scores' => $scoresJson,
            'total' => $total,
            'user_score' => $userScoreJson ?? null,
            ...cursor_for_response($cursorHelper->next($highScores, $hasMore)),
        ];
    }

    /**
     * Get a Score
     *
     * Returns detail of specified score and the surrounding scores.
     *
     * ---
     *
     * ### Response Format
     *
     * Returns [MultiplayerScore](#multiplayerscore) object.
     *
     * @urlParam room integer required Id of the room.
     * @urlParam playlist integer required Id of the playlist item.
     * @urlParam score integer required Id of the score.
     */
    public function show($roomId, $playlistId, $id)
    {
        $room = Room::find($roomId) ?? abort(404, 'Invalid room id');
        $playlistItem = $room->playlist()->find($playlistId) ?? abort(404, 'Invalid playlist id');
        $score = $playlistItem->scores()->findOrFail($id);

        return json_item(
            $score,
            'Multiplayer\Score',
            array_merge(['position', 'scores_around'], ScoreTransformer::BASE_INCLUDES)
        );
    }

    /**
     * Get User High Score
     *
     * Returns detail of highest score of specified user and the surrounding scores.
     *
     * ---
     *
     * ### Response Format
     *
     * Returns [MultiplayerScore](#multiplayerscore) object.
     *
     * @urlParam room integer required Id of the room.
     * @urlParam playlist integer required Id of the playlist item.
     * @urlParam user integer required User id.
     */
    public function showUser($roomId, $playlistId, $userId)
    {
        $room = Room::find($roomId) ?? abort(404, 'Invalid room id');
        $playlistItem = $room->playlist()->find($playlistId) ?? abort(404, 'Invalid playlist id');
        $score = $playlistItem->highScores()->where('user_id', $userId)->firstOrFail()->score ?? abort(404);

        return json_item(
            $score,
            'Multiplayer\Score',
            array_merge(['position', 'scores_around'], ScoreTransformer::BASE_INCLUDES)
        );
    }

    /**
     * @group Undocumented
     */
    public function store($roomId, $playlistId)
    {
        $room = Room::findOrFail($roomId);
        $playlistItem = $room->playlist()->where('id', $playlistId)->firstOrFail();
        $user = auth()->user();
        $params = request()->all();

        $buildId = ClientCheck::findBuild($user, $params)?->getKey()
            ?? config('osu.client.default_build_id');

        $score = $room->startPlay($user, $playlistItem, $buildId);

        return json_item($score, new ScoreTransformer());
    }

    /**
     * @group Undocumented
     */
    public function update($roomId, $playlistId, $scoreId)
    {
        $score = \DB::transaction(function () use ($roomId, $playlistId, $scoreId) {
            $room = Room::findOrFail($roomId);

            $score = $room
                ->scores()
                ->where([
                    'user_id' => \Auth::id(),
                    'playlist_item_id' => $playlistId,
                ])->with('playlistItem')
                ->lockForUpdate()
                ->findOrFail($scoreId);

            $params = SoloScore::extractParams(\Request::all(), $score);

            $room->completePlay($score, $params);

            return $score;
        });

        $soloScore = $score->soloScore;
        if ($soloScore->wasRecentlyCreated) {
            $soloScoreJson = json_item($soloScore, new SoloScoreTransformer(SoloScoreTransformer::TYPE_SOLO));
            $soloScore::queueForProcessing($soloScoreJson);
        }

        return json_item(
            $score,
            new ScoreTransformer(),
            ['position', 'scores_around', ...ScoreTransformer::BASE_INCLUDES],
        );
    }
}
