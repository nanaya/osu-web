<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Http\Controllers;

use App\Libraries\RulesetHelper;
use App\Models\Score\Best\Model as ScoreBest;
use App\Models\Solo\Score as SoloScore;
use App\Transformers\ScoreTransformer;
use App\Transformers\UserCompactTransformer;

class ScoresController extends Controller
{
    const REPLAY_DOWNLOAD_COUNT_INTERVAL = 86400; // 1 day

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', ['except' => [
            'show',
            'userRankLookup',
        ]]);

        $this->middleware('require-scopes:public');
    }

    public function download($rulesetOrSoloId, $id = null)
    {
        $shouldRedirect = !is_api_request() && !from_app_url();
        if ($id === null) {
            if ($shouldRedirect) {
                return ujs_redirect(route('scores.show', ['rulesetOrScore' => $rulesetOrSoloId]));
            }
            $soloScore = SoloScore::where('has_replay', true)->findOrFail($rulesetOrSoloId);

            $score = $soloScore->legacyScore() ?? $soloScore;
        } else {
            if ($shouldRedirect) {
                return ujs_redirect(route('scores.show', ['rulesetOrScore' => $rulesetOrSoloId, 'score' => $id]));
            }
            // don't limit downloading replays of restricted users for review purpose
            $score = ScoreBest::getClass($rulesetOrSoloId)
                ::where('score_id', $id)
                ->where('replay', true)
                ->firstOrFail();
        }

        $file = $score->getReplayFile();
        if ($file === null) {
            abort(404);
        }

        $currentUser = \Auth::user();
        if (
            !$currentUser->isRestricted()
            && $currentUser->getKey() !== $score->user_id
            && ($currentUser->token()?->client->password_client ?? false)
        ) {
            $countLock = \Cache::lock(
                "view:score_replay:{$score->getKey()}:{$currentUser->getKey()}",
                static::REPLAY_DOWNLOAD_COUNT_INTERVAL,
            );

            if ($countLock->get()) {
                $score->user->statistics($score->getMode(), true)->increment('replay_popularity');

                $currentMonth = format_month_column(new \DateTime());
                $score->user->replaysWatchedCounts()
                    ->firstOrCreate(['year_month' => $currentMonth], ['count' => 0])
                    ->incrementInstance('count');

                if ($score instanceof ScoreBest) {
                    $score->replayViewCount()
                        ->firstOrCreate([], ['play_count' => 0])
                        ->incrementInstance('play_count');
                }
            }
        }

        static $responseHeaders = [
            'Content-Type' => 'application/x-osu-replay',
        ];

        return response()->streamDownload(function () use ($file) {
            echo $file;
        }, $this->makeReplayFilename($score), $responseHeaders);
    }

    public function show($rulesetOrSoloId, $legacyId = null)
    {
        if ($legacyId === null) {
            $scoreQuery = SoloScore::whereKey($rulesetOrSoloId);
        } else {
            // `SoloScore` tables can have records with `legacy_score_id = 0`
            // which correspond to rows from `osu_scores_*` (non-high) tables.
            // do not attempt to perform lookups for zero to avoid weird results.
            // negative IDs should never occur (ID columns in score tables are all `bigint unsigned`).
            if ($legacyId <= 0) {
                abort(404, 'invalid score ID');
            }

            $scoreQuery = SoloScore::where([
                'ruleset_id' => RulesetHelper::toId($rulesetOrSoloId) ?? abort(404, 'unknown ruleset name'),
                'legacy_score_id' => $legacyId,
            ]);
        }
        $score = $scoreQuery->whereHas('beatmap.beatmapset')->visibleUsers()->firstOrFail();

        $userIncludes = array_map(function ($include) {
            return "user.{$include}";
        }, UserCompactTransformer::CARD_INCLUDES);

        $scoreJson = json_item($score, new ScoreTransformer(), array_merge([
            'beatmap.max_combo',
            'beatmap.user',
            'beatmapset',
            'rank_global',
        ], $userIncludes));

        if (is_json_request()) {
            return $scoreJson;
        }

        return ext_view('scores.show', compact('score', 'scoreJson'));
    }

    public function userRankLookup()
    {
        $params = get_params(request()->all(), null, [
            'beatmapId:int',
            'score:int',
            'rulesetId:int',
        ]);

        foreach (['beatmapId', 'score', 'rulesetId'] as $key) {
            if (!isset($params[$key])) {
                abort(422, "required parameter '{$key}' is missing");
            }
        }

        $score = ScoreBest
            ::getClassByRulesetId($params['rulesetId'])
            ::where([
                'beatmap_id' => $params['beatmapId'],
                'hidden' => false,
                'score' => $params['score'],
            ])->firstOrFail();

        return response()->json($score->userRank(['cached' => false]) - 1);
    }

    private function makeReplayFilename(ScoreBest|SoloScore $score): string
    {
        $prefix = $score instanceof SoloScore
            ? 'solo-replay'
            : 'replay';

        return "{$prefix}-{$score->getMode()}_{$score->beatmap_id}_{$score->getKey()}.osr";
    }
}
