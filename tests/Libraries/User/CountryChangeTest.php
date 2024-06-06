<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace Tests\Libraries\User;

use App\Exceptions\InvariantException;
use App\Libraries\RulesetHelper;
use App\Libraries\User\CountryChange;
use App\Models\Country;
use App\Models\Score\Best\Model as ScoreBestModel;
use App\Models\User;
use Tests\TestCase;

class CountryChangeTest extends TestCase
{
    /**
     * @group RequiresScoreIndexer
     */
    public function testDo(): void
    {
        $user = User::factory();
        foreach (RulesetHelper::NAME_TO_IDS as $ruleset => $_rulesetId) {
            $user = $user->withPlays(rand(1, 20), $ruleset);
        }
        $user = $user->create();
        foreach (RulesetHelper::NAME_TO_IDS as $ruleset => $_rulesetId) {
            ScoreBestModel
                ::getClass($ruleset)
                ::factory(['user_id' => $user, 'country_acronym' => $user->country_acronym])
                ->count(rand(1, 5))
                ->create();
        }
        $targetCountry = Country::factory()->create()->getKey();

        $this->expectCountChange(fn () => $user->accountHistories()->count(), 1);
        CountryChange::handle($user, $targetCountry, 'test');

        $user->refresh();
        $this->assertSame($user->country_acronym, $targetCountry);
        foreach (RulesetHelper::NAME_TO_IDS as $ruleset => $_rulesetId) {
            $this->assertSame($user->statistics($ruleset)->country_acronym, $targetCountry);

            foreach (RulesetHelper::VARIANTS[$ruleset] ?? [] as $variant) {
                $this->assertSame(
                    $user->statistics($ruleset, false, $variant)->country_acronym,
                    $targetCountry,
                );
            }

            foreach ($user->scoresBest($ruleset) as $score) {
                $this->assertSame($score->country_acronym, $targetCountry);
            }
        }

        // TODO: add test for solo score country change (in es index)
    }

    public function testDoInvalidCountry(): void
    {
        $user = User::factory()->create();
        $oldCountry = $user->country_acronym;

        $this->expectException(InvariantException::class);
        CountryChange::handle($user, '__', 'test');
    }
}
