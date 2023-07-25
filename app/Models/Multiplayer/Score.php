<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Models\Multiplayer;

use App\Exceptions\GameCompletedException;
use App\Exceptions\InvariantException;
use App\Models\Model;
use App\Models\Solo\Score as SoloScore;
use App\Models\Solo\ScoreData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property float|null $accuracy
 * @property int $beatmap_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $ended_at
 * @property int $id
 * @property int|null $max_combo
 * @property array|null $mods
 * @property bool|null $passed
 * @property PlaylistItem $playlistItem
 * @property int $playlist_item_id
 * @property float|null $pp
 * @property mixed|null $rank
 * @property Room $room
 * @property int $room_id
 * @property SoloScore $soloScore
 * @property int|null $solo_score_id
 * @property \Carbon\Carbon $started_at
 * @property \stdClass|null $statistics
 * @property int|null $total_score
 * @property \Carbon\Carbon|null $updated_at
 * @property User $user
 * @property int $user_id
 */
class Score extends Model
{
    use SoftDeletes;

    protected $casts = [
        'ended_at' => 'datetime',
        'mods' => 'object',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'statistics' => 'object',
    ];
    protected $table = 'multiplayer_scores';

    public static function start(array $params)
    {
        // TODO: move existence checks here?
        $score = new static($params);
        $score->started_at = Carbon::now();

        $score->save();

        return $score;
    }

    public function playlistItem()
    {
        return $this->belongsTo(PlaylistItem::class, 'playlist_item_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function soloScore(): BelongsTo
    {
        return $this->belongsTo(SoloScore::class, 'solo_score_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getAttribute($key)
    {
        return match ($key) {
            'accuracy',
            'beatmap_id',
            'build_id',
            'id',
            'max_combo',
            'mods',
            'passed',
            'playlist_item_id',
            'pp',
            'rank',
            'room_id',
            'solo_score_id',
            'total_score',
            'user_id' => $this->getRawAttribute($key),

            'statistics' => json_decode($this->getRawAttribute($key), true),

            'data' => $this->getData(),
            'ruleset_id' => $this->getRulesetId(),

            'created_at',
            'deleted_at',
            'ended_at',
            'started_at',
            'updated_at' => $this->getTimeFast($key),

            'created_at_json',
            'deleted_at_json',
            'ended_at_json',
            'started_at_json',
            'updated_at_json' => $this->getJsonTimeFast($key),

            'playlistItem',
            'room',
            'soloScore',
            'user' => $this->getRelationValue($key),
        };
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }

    public function scopeForPlaylistItem($query, $playlistItemId)
    {
        return $query->where('playlist_item_id', $playlistItemId);
    }

    public function isCompleted(): bool
    {
        return $this->getSoloScore() !== null;
    }

    public function complete(array $params)
    {
        $this->getConnection()->transaction(function () use ($params) {
            if ($this->isCompleted()) {
                throw new GameCompletedException('cannot modify score after submission');
            }

            $soloScore = SoloScore::createFromJsonOrExplode($params);
            $mods = $soloScore->data->mods;

            if (!empty($this->playlistItem->required_mods)) {
                $missingMods = array_diff(
                    array_column($this->playlistItem->required_mods, 'acronym'),
                    array_column($mods, 'acronym')
                );

                if (!empty($missingMods)) {
                    throw new InvariantException('This play does not include the mods required.');
                }
            }

            if (!empty($this->playlistItem->allowed_mods)) {
                $missingMods = array_diff(
                    array_column($mods, 'acronym'),
                    array_column($this->playlistItem->required_mods, 'acronym'),
                    array_column($this->playlistItem->allowed_mods, 'acronym')
                );

                if (!empty($missingMods)) {
                    throw new InvariantException('This play includes mods that are not allowed.');
                }
            }

            $this->soloScore()->associate($soloScore);
            $this->save();
        });
    }

    public function getSoloScore(): ?SoloScore
    {
        if ($this->solo_score_id !== null) {
            return $this->soloScore;
        }

        if ($this->ended_at !== null) {
            return new SoloScore([
                'beatmap_id' => $this->beatmap_id,
                'created_at' => $this->created_at_json,
                'data' => $this->data,
                'has_replay' => false,
                'preseve' => true,
                'ruleset_id' => $this->ruleset_id,
                'updated_at' => $this->updated_at_json,
                'user_id' => $this->user_id,
            ]);
        }

        return null;
    }

    public function userRank()
    {
        if ($this->total_score === null || $this->getKey() === null) {
            return;
        }

        $query = PlaylistItemUserHighScore
            ::where('playlist_item_id', $this->playlist_item_id)
            ->cursorSort('score_asc', [
                'total_score' => $this->total_score,
                'score_id' => $this->getKey(),
            ]);

        return 1 + $query->count();
    }

    private function getData(): ScoreData
    {
        return new ScoreData([
            ...$this->getAttributes(),
            'mods' => $this->mods,
            'passed' => $this->passed,
            'ruleset_id' => $this->ruleset_id,
            'statistics' => $this->statistics,
        ]);
    }

    private function getRulesetId(): int
    {
        return $this->playlistItem->ruleset_id;
    }
}
