<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Console\Commands;

use App\Libraries\RulesetHelper;
use App\Models\Beatmapset;
use Illuminate\Console\Command;

class ModdingRankCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'modding:rank {--no-wait} {--count-only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rank maps in queue.';

    private bool $countOnly = false;
    private bool $noWait = false;

    public static function getStats(int $rulesetId)
    {
        $rankedTodayCount = Beatmapset::ranked()
            ->withoutTrashed()
            ->withModesForRanking($rulesetId)
            ->where('approved_date', '>=', now()->subDays())
            ->count();

        return [
            'availableQuota' => $GLOBALS['cfg']['osu']['beatmapset']['rank_per_day'] - $rankedTodayCount,
            'inQueue' => Beatmapset::toBeRanked($rulesetId)->count(),
            'rankedToday' => $rankedTodayCount,
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->countOnly = get_bool($this->option('count-only'));
        $this->noWait = get_bool($this->option('no-wait'));

        if ($this->countOnly) {
            $this->info('Number of beatmapsets in queue:');
        } else {
            $this->info('Ranking beatmapsets...');
        }

        $rulesetIds = array_values(RulesetHelper::NAME_TO_IDS);
        shuffle($rulesetIds);

        foreach ($rulesetIds as $rulesetId) {
            $this->waitRandom();

            if ($this->countOnly) {
                $stats = static::getStats($rulesetId);
                $this->info(RulesetHelper::toName($ruleset));
                foreach ($stats as $key => $value) {
                    $this->line("{$key}: {$value}");
                }
                $this->newLine();
            } else {
                $this->rankAll($rulesetId);
            }
        }

        $this->info('Done');
    }

    private function rankAll(int $rulesetId)
    {
        $this->info('Ranking beatmapsets with at least mode: '.RulesetHelper::toName($rulesetId));
        $stats = static::getStats($rulesetId);

        $this->info("{$stats['rankedToday']} beatmapsets ranked last 24 hours. Can rank {$stats['availableQuota']} more");

        if ($stats['availableQuota'] <= 0) {
            return;
        }

        $toRankLimit = min($GLOBALS['cfg']['osu']['beatmapset']['rank_per_run'], $stats['availableQuota']);

        $toBeRanked = Beatmapset::tobeRanked($rulesetId)
            ->orderBy('queued_at', 'ASC')
            ->limit($toRankLimit)
            ->get();

        $this->info("{$stats['inQueue']} beatmapset(s) in ranking queue");
        $this->info("Ranking {$toBeRanked->count()} beatmapset(s)");

        foreach ($toBeRanked as $beatmapset) {
            $this->waitRandom();
            $this->info("Ranking beatmapset: {$beatmapset->getKey()}");
            $beatmapset->rank();
        }
    }

    private function waitRandom()
    {
        if ($this->noWait || $this->countOnly) {
            return;
        }

        $delay = rand(5, 120);
        $this->info("Pausing for {$delay} seconds...");
        sleep($delay);
    }
}
