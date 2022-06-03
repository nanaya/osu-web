<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Models\Solo;

use App\Libraries\Score\UserRankCache;
use App\Models\Beatmap;
use App\Models\Model;
use App\Models\Score as LegacyScore;
use App\Models\Traits;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use LaravelRedis;

/**
 * @property int $beatmap_id
 * @property \Carbon\Carbon|null $created_at
 * @property \stdClass $data
 * @property \Carbon\Carbon|null $deleted_at
 * @property int $id
 * @property bool $preserve
 * @property int $ruleset_id
 * @property \Carbon\Carbon|null $updated_at
 * @property User $user
 * @property int $user_id
 */
class Score extends Model implements Traits\ReportableInterface
{
    use Traits\Reportable, Traits\WithWeightedPp;

    const PROCESSING_QUEUE = 'osu-queue:score-statistics';

    public ?int $position = null;
    public ?float $weight = null;

    protected $table = 'solo_scores';
    protected $casts = [
        'data' => ScoreData::class,
        'has_replay' => 'boolean',
        'preserve' => 'boolean',
    ];

    public static function createFromJsonOrExplode(array $params)
    {
        $score = new static([
            'beatmap_id' => $params['beatmap_id'],
            'ruleset_id' => $params['ruleset_id'],
            'user_id' => $params['user_id'],
            'data' => $params,
        ]);

        $score->data->assertCompleted();

        // this should potentially just be validation rather than applying this logic here, but
        // older lazer builds potentially submit incorrect details here (and we still want to
        // accept their scores.
        if (!$score->data->passed) {
            $score->data->rank = 'D';
        }

        $score->saveOrExplode();

        return $score;
    }

    /**
     * Queue the item for score processing
     *
     * @param array $scoreJson JSON of the score generated using ScoreTransformer of type Solo
     */
    public static function queueForProcessing(array $scoreJson): void
    {
        LaravelRedis::lpush(static::PROCESSING_QUEUE, json_encode([
            'Score' => [
                'beatmap_id' => $scoreJson['beatmap_id'],
                'id' => $scoreJson['id'],
                'ruleset_id' => $scoreJson['ruleset_id'],
                'user_id' => $scoreJson['user_id'],
                // TODO: processor is currently order dependent and requires
                // this to be located at the end
                'data' => json_encode($scoreJson),
            ],
        ]));
    }

    public function scopeForRuleset(Builder $query, string $ruleset): Builder
    {
        return $query->where('ruleset_id', Beatmap::MODES[$ruleset]);
    }

    public function scopeIncludeFails(Builder $query, bool $includeFails): Builder
    {
        if (!$includeFails) {
            $query->where('data->passed', true);
        }

        return $query;
    }

    public function beatmap()
    {
        return $this->belongsTo(Beatmap::class, 'beatmap_id');
    }

    public function performance()
    {
        return $this->hasOne(ScorePerformance::class, 'score_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * This should match the one used in osu-elastic-indexer.
     */
    public function scopeIndexable(Builder $query): Builder
    {
        return $this
            ->where('preserve', true)
            ->whereHas('user', fn (Builder $q): Builder => $q->default());
    }

    public function getPpAttribute(): ?float
    {
        return $this->performance?->pp;
    }

    public function createLegacyEntryOrExplode()
    {
        $score = $this->makeLegacyEntry();

        $score->saveOrExplode();

        return $score;
    }

    public function getMode(): string
    {
        return Beatmap::modeStr($this->ruleset_id);
    }

    public function legacyScore(): ?LegacyScore\Best\Model
    {
        $id = $this->data->legacyScoreId;

        return $id === null
            ? null
            : LegacyScore\Best\Model::getClass($this->ruleset_id)::find($id);
    }

    public function makeLegacyEntry(): LegacyScore\Model
    {
        $data = $this->data;
        $statistics = $data->statistics;
        $scoreClass = LegacyScore\Model::getClass($this->ruleset_id);

        $score = new $scoreClass([
            'beatmap_id' => $this->beatmap_id,
            'beatmapset_id' => $this->beatmap?->beatmapset_id ?? 0,
            'countmiss' => $statistics->miss,
            'enabled_mods' => app('mods')->idsToBitset(array_column($data->mods, 'acronym')),
            'maxcombo' => $data->maxCombo,
            'pass' => $data->passed,
            'perfect' => $data->passed && $statistics->miss + $statistics->largeTickMiss === 0,
            'rank' => $data->rank,
            'score' => $data->totalScore,
            'scorechecksum' => "\0",
            'user_id' => $this->user_id,
        ]);

        switch (Beatmap::modeStr($this->ruleset_id)) {
            case 'osu':
                $score->count300 = $statistics->great;
                $score->count100 = $statistics->ok;
                $score->count50 = $statistics->meh;
                break;
            case 'taiko':
                $score->count300 = $statistics->great;
                $score->count100 = $statistics->ok;
                break;
            case 'fruits':
                $score->count300 = $statistics->great;
                $score->count100 = $statistics->largeTickHit;
                $score->countkatu = $statistics->smallTickMiss;
                $score->count50 = $statistics->smallTickHit;
                break;
            case 'mania':
                $score->countgeki = $statistics->perfect;
                $score->count300 = $statistics->great;
                $score->countkatu = $statistics->good;
                $score->count100 = $statistics->ok;
                $score->count50 = $statistics->meh;
                break;
        }

        return $score;
    }

    public function trashed(): bool
    {
        return false;
    }

    public function userRank(): ?int
    {
        return UserRankCache::fetch([], $this->beatmap_id, $this->ruleset_id, $this->data->totalScore);
    }

    public function weightedPp(): ?float
    {
        if ($this->weight === null) {
            return null;
        }

        $pp = $this->performance?->pp;

        return $pp === null ? null : $this->weight * $pp;
    }

    protected function newReportableExtraParams(): array
    {
        return [
            'reason' => 'Cheating',
            'user_id' => $this->user_id,
        ];
    }
}
