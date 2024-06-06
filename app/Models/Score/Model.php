<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Models\Score;

use App\Exceptions\ClassNotFoundException;
use App\Libraries\Mods;
use App\Libraries\RulesetHelper;
use App\Models\Beatmap;
use App\Models\Model as BaseModel;
use App\Models\Solo\ScoreData;
use App\Models\Traits\Scoreable;
use App\Models\User;

/**
 * @property Beatmap $beatmap
 * @property User $user
 */
abstract class Model extends BaseModel
{
    use Scoreable;

    public $timestamps = false;

    protected $casts = [
        'date' => 'datetime',
        'pass' => 'bool',
        'perfect' => 'bool',
        'replay' => 'bool', // for best model
    ];
    protected $primaryKey = 'score_id';

    public static function getClassByRulesetId(int $rulesetId): ?string
    {
        $ruleset = RulesetHelper::toName($rulesetId);

        if ($ruleset !== null) {
            return static::getClass($ruleset);
        }

        return null;
    }

    public static function getClass(string $ruleset): string
    {
        if (!RulesetHelper::isValidName($ruleset)) {
            throw new ClassNotFoundException();
        }

        return get_class_namespace(static::class).'\\'.studly_case($ruleset);
    }

    public function scopeDefault($query)
    {
        return $query
            ->whereHas('beatmap.beatmapset')
            ->orderBy('score_id', 'desc');
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->user_id);
    }

    public function scopeIncludeFails($query, bool $include)
    {
        if ($include) {
            return $query;
        }

        return $query->where('pass', true);
    }

    public function scopeVisibleUsers($query)
    {
        return $query->whereHas('user', function ($userQuery) {
            $userQuery->default();
        });
    }

    public function scopeWithMods($query, $modsArray)
    {
        return $query->where(function ($q) use ($modsArray) {
            $bitset = app('mods')->idsToBitset($modsArray);
            $preferenceMask = ~Mods::LEGACY_PREFERENCE_MODS_BITSET;

            if (in_array('NM', $modsArray, true)) {
                $q->orWhereRaw('enabled_mods & ? = 0', [$preferenceMask]);
            }

            if ($bitset > 0) {
                $q->orWhereRaw('enabled_mods & ? = ?', [$preferenceMask | $bitset, $bitset]);
            }
        });
    }

    public function scopeWithoutMods($query, $modsArray)
    {
        $bitset = app('mods')->idsToBitset($modsArray);

        return $query->whereRaw('enabled_mods & ? = 0', $bitset);
    }

    public function beatmap()
    {
        return $this->belongsTo(Beatmap::class, 'beatmap_id');
    }

    public function best()
    {
        $basename = get_class_basename(static::class);

        return $this->belongsTo("App\\Models\\Score\\Best\\{$basename}", 'high_score_id', 'score_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getAttribute($key)
    {
        return match ($key) {
            'beatmap_id',
            'beatmapset_id',
            'count100',
            'count300',
            'count50',
            'countgeki',
            'countkatu',
            'countmiss',
            'high_score_id',
            'maxcombo',
            'rank',
            'score',
            'score_id',
            'scorechecksum',
            'user_id' => $this->getRawAttribute($key),

            'hidden',
            'pass',
            'perfect' => (bool) $this->getRawAttribute($key),

            'date' => $this->getTimeFast($key),

            'date_json' => $this->getJsonTimeFast($key),

            'enabled_mods' => $this->getEnabledModsAttribute($this->getRawAttribute('enabled_mods')),

            'best_id' => $this->getRawAttribute('high_score_id'),
            'has_replay' => $this->best?->replay,
            'pp' => $this->best?->pp,

            'beatmap',
            'best',
            'replayViewCount',
            'user' => $this->getRelationValue($key),

            default => $this->getNewScoreAttribute($key),
        };
    }

    public function getNewScoreAttribute(string $key)
    {
        return match ($key) {
            'accuracy' => $this->accuracy(),
            'build_id' => null,
            'data' => $this->getData(),
            'ended_at_json' => $this->date_json,
            'is_perfect_combo' => $this->perfect,
            'legacy_perfect' => $this->perfect,
            'legacy_score_id' => $this->getKey(),
            'legacy_total_score' => $this->score,
            'max_combo' => $this->maxcombo,
            'passed' => $this->pass,
            'ruleset_id' => RulesetHelper::toId($this->getMode()),
            'started_at_json' => null,
            'total_score' => $this->score,
        };
    }

    public function getMode(): string
    {
        return snake_case(get_class_basename(static::class));
    }

    public function getData(): ScoreData
    {
        $mods = array_map(fn ($m) => ['acronym' => $m, 'settings' => []], $this->enabled_mods);

        $statistics = [
            'miss' => $this->countmiss,
            'great' => $this->count300,
        ];
        switch ($this->getMode()) {
            case 'osu':
                $statistics['ok'] = $this->count100;
                $statistics['meh'] = $this->count50;
                break;
            case 'taiko':
                $statistics['ok'] = $this->count100;
                break;
            case 'catch':
                $statistics['large_tick_hit'] = $this->count100;
                $statistics['small_tick_hit'] = $this->count50;
                $statistics['small_tick_miss'] = $this->countkatu;
                break;
            case 'mania':
                $statistics['perfect'] = $this->countgeki;
                $statistics['good'] = $this->countkatu;
                $statistics['ok'] = $this->count100;
                $statistics['meh'] = $this->count50;
                break;
        }

        return new ScoreData(compact('mods', 'statistics'));
    }
}
