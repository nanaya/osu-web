<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Http\Controllers\Solo;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Solo\Score;
use App\Models\Solo\ScoreToken;
use App\Transformers\ScoreTransformer;
use DB;

class ScoresController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store($beatmapId, $tokenId)
    {
        $score = DB::transaction(function () use ($beatmapId, $tokenId) {
            $user = auth()->user();
            $scoreToken = ScoreToken::where([
                'beatmap_id' => $beatmapId,
                'user_id' => $user->getKey(),
            ])->lockForUpdate()->findOrFail($tokenId);

            // return existing score otherwise (assuming duplicated submission)
            if ($scoreToken->score_id === null) {
                $params = Score::extractParams(\Request::all(), $scoreToken);
                $score = Score::createFromJsonOrExplode($params);
                $score->createLegacyEntryOrExplode();
                $scoreToken->fill(['score_id' => $score->getKey()])->saveOrExplode();
            } else {
                // assume score exists and is valid
                $score = $scoreToken->score;
            }

            return $score;
        });

        $scoreJson = json_item($score, new ScoreTransformer(ScoreTransformer::TYPE_SOLO));
        if ($score->wasRecentlyCreated) {
            $score::queueForProcessing($scoreJson);
        }

        return $scoreJson;
    }
}
