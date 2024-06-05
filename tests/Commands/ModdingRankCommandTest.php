<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace Tests\Commands;

use App\Console\Commands\ModdingRankCommand;
use App\Jobs\CheckBeatmapsetCovers;
use App\Jobs\Notifications\BeatmapsetRank;
use App\Libraries\RulesetHelper;
use App\Models\Beatmap;
use App\Models\BeatmapDiscussion;
use App\Models\Beatmapset;
use Bus;
use Database\Factories\BeatmapsetFactory;
use Tests\TestCase;

class ModdingRankCommandTest extends TestCase
{
    public function testCountOnly(): void
    {
        $this->beatmapset(['osu'])->create();

        $this->expectCountChange(fn () => Beatmapset::ranked()->count(), 0);

        $this->artisan('modding:rank', ['--count-only' => true]);

        Bus::assertNotDispatched(CheckBeatmapsetCovers::class);
        Bus::assertNotDispatched(BeatmapsetRank::class);
    }

    /**
     * @dataProvider rankDataProvider
     */
    public function testRank(int $qualifiedDaysAgo, int $expected): void
    {
        $this->beatmapset(['osu'], $qualifiedDaysAgo)->create();

        $this->expectCountChange(fn () => Beatmapset::ranked()->count(), $expected);

        $this->artisan('modding:rank', ['--no-wait' => true]);

        Bus::assertDispatchedTimes(CheckBeatmapsetCovers::class, $expected);
        Bus::assertDispatchedTimes(BeatmapsetRank::class, $expected);
    }

    /**
     * @dataProvider rankHybridDataProvider
     */
    public function testRankHybrid(array $beatmapsetRulesets, array $expectedCounts): void
    {
        foreach ($beatmapsetRulesets as $rulesets) {
            $this->beatmapset($rulesets)->create();
        }

        foreach (RulesetHelper::NAME_TO_IDS as $_ruleset => $rulesetId) {
            $this->assertSame($expectedCounts[$rulesetId], ModdingRankCommand::getStats($rulesetId)['inQueue']);
        }
    }

    public function testRankOpenIssue(): void
    {
        $this->beatmapset(['osu'])
            ->has(BeatmapDiscussion::factory()->general()->problem())
            ->create();

        $this->expectCountChange(fn () => Beatmapset::ranked()->count(), 0);

        $this->artisan('modding:rank', ['--no-wait' => true]);

        Bus::assertNotDispatched(CheckBeatmapsetCovers::class);
        Bus::assertNotDispatched(BeatmapsetRank::class);
    }

    public function testRankQuota(): void
    {
        $this->beatmapset(['osu'])->count(3)->create();

        $this->expectCountChange(fn () => Beatmapset::qualified()->count(), -2);
        $this->expectCountChange(fn () => Beatmapset::ranked()->count(), 2);

        $this->artisan('modding:rank', ['--no-wait' => true]);

        Bus::assertDispatched(CheckBeatmapsetCovers::class);
        Bus::assertDispatched(BeatmapsetRank::class);
    }

    public function testRankQuotaSeparateRuleset(): void
    {
        foreach (RulesetHelper::NAME_TO_IDS as $ruleset => $_rulesetId) {
            $this->beatmapset([$ruleset])->create();
        }

        $count = count(RulesetHelper::NAME_TO_IDS);
        $this->expectCountChange(fn () => Beatmapset::ranked()->count(), $count);

        $this->artisan('modding:rank', ['--no-wait' => true]);

        Bus::assertDispatchedTimes(CheckBeatmapsetCovers::class, $count);
        Bus::assertDispatchedTimes(BeatmapsetRank::class, $count);
    }


    public static function rankDataProvider()
    {
        // 1 day ago isn't used because it might or might not be equal to the cutoff depending on how fast it runs.
        return [
            [0, 0],
            [2, 1],
        ];
    }

    public static function rankHybridDataProvider()
    {
        return [
            // hybrid counts as ruleset with lowest enum value
            [[['osu', 'taiko', 'catch', 'mania']], [1, 0, 0, 0]],
            [[['taiko', 'catch', 'mania']], [0, 1, 0, 0]],
            [[['catch', 'mania']], [0, 0, 1, 0]],
            [[['mania']], [0, 0, 0, 1]],

            // not comprehensive
            [[['osu', 'taiko'], ['osu']], [2, 0, 0, 0]],
            [[['osu', 'taiko'], ['taiko']], [1, 1, 0, 0]],
            [[['mania', 'taiko'], ['taiko']], [0, 2, 0, 0]],
            [[['mania', 'taiko'], ['mania']], [0, 1, 0, 1]],
            [[['catch', 'taiko'], ['mania']], [0, 1, 0, 1]],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config_set('osu.beatmapset.minimum_days_for_rank', 1);
        config_set('osu.beatmapset.rank_per_day', 2);

        Bus::fake([BeatmapsetRank::class, CheckBeatmapsetCovers::class]);
    }

    /**
     * @param string[] $rulesets
     */
    protected function beatmapset(array $rulesets, int $qualifiedDaysAgo = 2): BeatmapsetFactory
    {
        $factory = Beatmapset::factory()
            ->owner()
            ->qualified(now()->subDays($qualifiedDaysAgo));

        foreach ($rulesets as $ruleset) {
            $factory = $factory->has(Beatmap::factory()->ruleset($ruleset));
        }

        return $factory;
    }
}
