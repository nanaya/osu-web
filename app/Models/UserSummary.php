<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Models;

use App\Traits\Memoizes;
use App\Transformers\ScoreTransformer;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property \DateTimeInterface $created_at
 * @property int $id
 * @property array $summary_data
 * @property string $share_key
 * @property \DateTimeInterface $start_time
 * @property \DateTimeInterface $updated_at
 * @property-read User $user
 * @property int $user_id
 */
class UserSummary extends Model
{
    use Memoizes;

    protected $casts = [
        'start_time' => 'datetime',
        'summary_data' => 'array',
    ];

    private static function userTransformer(User $user): array
    {
        return [
            'avatar_url' => $user->user_avatar,
            'cover_url' => $user->cover()->url(),
            'id' => $user->getKey(),
            'username' => $user->username,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function endTime(): \DateTimeInterface
    {
        return $this->memoize(__FUNCTION__, fn () => $this->start_time->endOfYear());
    }

    public function generate(): array
    {
        return [
            'daily_challenge' => $this->generateDailyChallengeSummary(),
            'favorite_artists' => $this->generateFavouriteArtistsSummary(),
            'favorite_mappers' => $this->generateFavouriteMappersSummary(),
            'mapping' => $this->generateMappingSummary(),
            'medals' => $this->generateMedalsSummary(),
            'replays' => $this->generateReplaysSummary(),
            'scores' => $this->generateScoresSummary(),
            'top_plays' => $this->generateTopPlaysSummary(),
            'user' => static::userTransformer($this->user),
        ];
    }

    public function generateDailyChallengeSummary(): array
    {
        $highScores = Multiplayer\PlaylistItemUserHighScore
            ::where('user_id', $this->user_id)
            ->passing()
            ->whereHas(
                'playlistItem.room',
                fn ($q) => $q
                    ->dailyChallenges()
                    ->whereBetween('starts_at', $this->timeRange()),
            )->with('playlistItem.room')
            ->get()
            ->sortBy('playlistItem.room.starts_at');

        $ret = ['cleared' => 0, 'top_10p' => 0, 'top_50p' => 0, 'highest_streak' => 0];
        $lastDate = null;
        $currentStreak = 0;
        foreach ($highScores as $highScore) {
            $ret['cleared']++;

            foreach ($highScore->playlistItem->scorePercentile() as $p => $totalScore) {
                if ($highScore->total_score >= $totalScore) {
                    $ret[$p]++;
                }
            }

            $room = $highScore->playlistItem->room;
            if ($lastDate === null) {
                $currentStreak++;
            } else {
                if ($room->starts_at->startOfDay()->diffInDays($lastDate) === -1.0) {
                    $currentStreak++;
                } else {
                    $currentStreak = 1;
                }
            }
            $lastDate = $room->starts_at->startOfDay();
            if ($currentStreak > $ret['highest_streak']) {
                $ret['highest_streak'] = $currentStreak;
            }
        }

        return $ret;
    }

    public function generateFavouriteArtistsSummary(): array
    {
        $scores = $this->userScores();

        $scoresByArtist = [];
        foreach ($scores as $score) {
            $beatmapset = $score->beatmap->beatmapset;
            $artist = $beatmapset->track?->artist;
            $artistName = $artist?->name ?? $beatmapset->artist;
            $scoresByArtist[$artistName] ??= [
                'artist' => [
                    'id' => $artist?->getKey(),
                    'name' => $artistName,
                ],
                'scores' => [],
            ];
            $scoresByArtist[$artistName]['scores'][] = $score;
        }
        usort($scoresByArtist, fn ($a, $b) => count($b['scores']) - count($a['scores']));
        $scoresByArtist = array_slice($scoresByArtist, 0, 10);

        $ret = [];
        foreach ($scoresByArtist as $scores) {
            $ret[] = [
                'artist' => $scores['artist'],
                'scores' => $this->summariseHighScores($scores['scores']),
            ];
        }

        return $ret;
    }

    public function generateFavouriteMappersSummary(): array
    {
        $scores = $this->userScores();

        $scoresByMapper = [];
        foreach ($scores as $score) {
            foreach ($score->beatmap->getOwners() as $mapper) {
                if (!($mapper instanceof DeletedUser)) {
                    $mapperId = $mapper->getKey();
                    $scoresByMapper[$mapperId] ??= [
                        'mapper' => $mapper,
                        'scores' => [],
                    ];
                    $scoresByMapper[$mapperId]['scores'][] = $score;
                }
            }
        }
        usort($scoresByMapper, fn ($a, $b) => count($b['scores']) - count($a['scores']));
        $scoresByMapper = array_slice($scoresByMapper, 0, 10);

        $ret = [];
        foreach ($scoresByMapper as $scores) {
            $ret[] = [
                'mapper' => static::userTransformer($scores['mapper']),
                'scores' => $this->summariseHighScores($scores['scores']),
            ];
        }

        return $ret;
    }

    public function generateMappingSummary(): array
    {
        $timeScope = fn ($q) => $q
            ->whereBetween('submit_date', $this->timeRange())
            ->orWhereBetween('approved_date', $this->timeRange());

        $ownMaps = $this->user->beatmapsets()->where($timeScope)->get();
        $ownMapsByApproved = $ownMaps->groupBy('approved');

        $discussionsCount = $this->user->beatmapDiscussions()
            ->whereBetween('created_at', $this->timeRange())
            ->count();

        $guestMaps = $this
            ->user
            ->profileBeatmapsetsGuest()
            ->where($timeScope)
            ->whereIn('approved', [Beatmapset::STATES['ranked'], Beatmapset::STATES['loved']])
            ->get();

        $kudosu = (int) $this
            ->user
            ->receivedKudosu()
            ->whereBetween('date', $this->timeRange())
            ->sum('amount');

        $nominations = $this
            ->user
            ->beatmapsetNominations()
            ->current()
            ->whereBetween('created_at', $this->timeRange())
            ->whereHas('beatmapset', fn ($q) => $q->whereIn('approved', [
                Beatmapset::STATES['ranked'],
                Beatmapset::STATES['approved'],
            ]))->count();

        return [
            'created' => $ownMaps->count(),
            'discussions' => $discussionsCount,
            'guest' => $guestMaps->count(),
            'kudosu' => $kudosu,
            'loved' => $ownMapsByApproved->get(Beatmapset::STATES['loved'])?->count() ?? 0,
            'nominations' => $nominations,
            'ranked' => $ownMapsByApproved->get(Beatmapset::STATES['ranked'])?->count() ?? 0,
        ];
    }

    public function generateMedalsSummary(): int
    {
        return $this
            ->user
            ->userAchievements()
            ->whereBetween('date', $this->timeRange())
            ->count();
    }

    public function generateReplaysSummary(): int
    {
        return (int) $this
            ->user
            ->replaysWatchedCounts()
            ->whereBetween('year_month', array_map(
                fn ($t) => $t->format('ym'),
                $this->timeRange(),
            ))->sum('count');
    }

    public function generateScoresSummary(): array
    {
        $scores = $this->userScores();

        $summary = [
            'acc' => 0,
            'combo' => 0,
            'pp' => 0,
            'score' => 0,
        ];
        foreach ($scores as $score) {
            if ($summary['combo'] < $score->max_combo) {
                $summary['combo'] = $score->max_combo;
            }
            if ($summary['score'] < $score->total_score) {
                $summary['score'] = $score->total_score;
            }
            $summary['acc'] += $score->accuracy;
        }
        $summary['acc'] /= max(1, count($scores));

        $scoresByBeatmapId = $this->userScoresBestPpByBeatmapId();
        foreach ($scoresByBeatmapId as $score) {
            $summary['pp'] += $score->pp;
        }

        return $summary;
    }

    public function generateTopPlaysSummary(): array
    {
        return json_collection($this
            ->userScoresBestPpByBeatmapId()
            ->sortByDesc('pp')
            ->slice(0, 20)
            ->values()
            ->all(), new ScoreTransformer(), ['beatmap.beatmapset', 'user']);
    }

    public function userScores(): EloquentCollection
    {
        return $this->memoize(
            __FUNCTION__,
            fn () => $this
                ->user
                ->soloScores()
                ->where('preserve', true)
                ->whereBetween('ended_at', $this->timeRange())
                ->whereHas('beatmap', fn ($q) => $q->scoreable())
                ->get(),
        );
    }

    public function userScoresBestPpByBeatmapId(): Collection
    {
        return $this->memoize(__FUNCTION__, function () {
            $scoresByBeatmapId = [];
            foreach ($this->userScores() as $score) {
                if ($score->pp !== null) {
                    $currentHighScore = $scoresByBeatmapId[$score->beatmap_id] ?? null;
                    if ($currentHighScore === null) {
                        $scoresByBeatmapId[$score->beatmap_id] = $score;
                    } else {
                        if ($score->pp > $currentHighScore->pp) {
                            $scoresByBeatmapId[$score->beatmap_id] = $score;
                        }
                    }
                }
            }

            return collect($scoresByBeatmapId);
        });
    }

    public function summariseHighScores(array $scores): array
    {
        $ret = [
            'pp_avg' => 0,
            'pp_best' => 0,
            'score_avg' => 0,
            'score_count' => count($scores),
        ];
        $ppScoreCount = 0;
        foreach ($scores as $score) {
            $pp = $score->pp;
            if ($pp !== null) {
                if ($pp > $ret['pp_best']) {
                    $ret['pp_best'] = $pp;
                }
                $ret['pp_avg'] += $pp;
                $ppScoreCount++;
            }
            $ret['score_avg'] += $score->total_score;
        }
        $ret['score_avg'] /= max(1, $ret['score_count']);
        $ret['pp_avg'] /= max(1, $ppScoreCount);

        return $ret;
    }

    public function timeRange(): array
    {
        return $this->memoize(__FUNCTION__, fn () => [
            $this->start_time,
            $this->endTime(),
        ]);
    }
}
