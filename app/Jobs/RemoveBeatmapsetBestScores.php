<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Jobs;

use App\Libraries\Elasticsearch\BoolQuery;
use App\Libraries\Elasticsearch\Es;
use App\Libraries\RulesetHelper;
use App\Models\Beatmapset;
use App\Models\Score\Best\Model;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class RemoveBeatmapsetBestScores implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 36000;
    public $beatmapset;
    public $maxScoreIds = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Beatmapset $beatmapset)
    {
        $this->beatmapset = $beatmapset;

        foreach (RulesetHelper::NAME_TO_IDS as $mode => $_modeInt) {
            $this->maxScoreIds[$mode] = Model::getClass($mode)::max('score_id');
        }
    }

    public function displayName()
    {
        return static::class." (Beatmapset {$this->beatmapset->getKey()})";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $beatmapIds = model_pluck($this->beatmapset->beatmaps(), 'beatmap_id');

        foreach (RulesetHelper::NAME_TO_IDS as $mode => $_modeInt) {
            $query = new BoolQuery();
            $query->filter(['terms' => ['beatmap_id' => $beatmapIds]]);
            $query->filter(['range' => ['score_id' => ['lte' => $this->maxScoreIds[$mode]]]]);

            // TODO: do something with response?
            Es::getClient('scores')->deleteByQuery([
                'index' => $GLOBALS['cfg']['osu']['elasticsearch']['prefix']."high_scores_{$mode}",
                'body' => ['query' => $query->toArray()],
                'client' => ['ignore' => 404],
            ]);

            $class = Model::getClass($mode);
            // Just delete until no more matching rows.
            $query = $class
                ::with('user')
                ->whereIn('beatmap_id', $beatmapIds)
                ->where('score_id', '<=', $this->maxScoreIds[$mode] ?? 0)
                ->orderBy('score', 'ASC')
                ->limit(1000);
            $scores = $query->get();

            while ($scores->count() > 0) {
                $scores->each->delete();
                $scores = $query->get();
            }
        }
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping((string) $this->beatmapset->getKey(), $this->timeout, $this->timeout)];
    }
}
