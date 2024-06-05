<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Singletons;

use App\Exceptions\InvariantException;
use App\Libraries\RulesetHelper;
use Tests\TestCase;

class ModsTest extends TestCase
{
    public function testModSettings()
    {
        $settings = app('mods')->filterSettings(RulesetHelper::NAME_TO_IDS['osu'], 'WU', ['initial_rate' => '1']);

        $this->assertSame(1.0, $settings->initial_rate);
    }

    public function testModSettingsInvalid()
    {
        $this->expectException(InvariantException::class);
        app('mods')->filterSettings(RulesetHelper::NAME_TO_IDS['osu'], 'WU', ['x' => '1']);
    }

    public function testParseInputArray()
    {
        $input = [['acronym' => 'WU', 'settings' => []]];
        $parsed = app('mods')->parseInputArray(RulesetHelper::NAME_TO_IDS['osu'], $input);

        $this->assertSame(1, count($parsed));
        $this->assertSame(0, count((array) $parsed[0]->settings));
        $this->assertSame('WU', $parsed[0]->acronym);
    }

    public function testParseInputArrayInvalidMod()
    {
        $input = [['acronym' => 'XYZ', 'settings' => []]];

        $this->expectException(InvariantException::class);
        app('mods')->parseInputArray(RulesetHelper::NAME_TO_IDS['osu'], $input);
    }

    public function testParseInputArrayWithSettings()
    {
        $input = [['acronym' => 'WU', 'settings' => ['initial_rate' => '1', 'adjust_pitch' => false]]];
        $parsed = app('mods')->parseInputArray(RulesetHelper::NAME_TO_IDS['osu'], $input);

        $this->assertSame(1, count($parsed));
        $this->assertSame(2, count((array) $parsed[0]->settings));
        $this->assertSame(1.0, $parsed[0]->settings->initial_rate);
        $this->assertSame(false, $parsed[0]->settings->adjust_pitch);
        $this->assertSame('WU', $parsed[0]->acronym);
    }

    public function testParseInputArrayWithSettingsInvalid()
    {
        $input = [['acronym' => 'WU', 'settings' => ['x' => '1']]];

        $this->expectException(InvariantException::class);
        app('mods')->parseInputArray(RulesetHelper::NAME_TO_IDS['osu'], $input);
    }

    public function testValidateSelectionWithInvalidRuleset()
    {
        $this->expectException(InvariantException::class);
        app('mods')->validateSelection(-1, []);
    }

    /**
     * @dataProvider modComboExclusives
     */
    public function testAssertValidExclusivity(int $rulesetId, array $requiredIds, array $allowedIds, bool $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvariantException::class);
        }

        $result = app('mods')->assertValidExclusivity($rulesetId, $requiredIds, $allowedIds);

        if ($isValid) {
            $this->assertTrue($result);
        }
    }

    /**
     * @dataProvider modCombos
     */
    public function testValidateSelection(int $rulesetId, $modCombo, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvariantException::class);
        }

        $result = app('mods')->validateSelection($rulesetId, $modCombo);

        if ($isValid) {
            $this->assertTrue($result);
        }
    }

    public static function modCombos()
    {
        return [
            // valid
            [RulesetHelper::NAME_TO_IDS['osu'], ['HD', 'DT'], true],
            [RulesetHelper::NAME_TO_IDS['osu'], ['HD', 'HR'], true],
            [RulesetHelper::NAME_TO_IDS['osu'], ['HD', 'HR'], true],
            [RulesetHelper::NAME_TO_IDS['osu'], ['HD', 'NC'], true],

            [RulesetHelper::NAME_TO_IDS['taiko'], ['HD', 'NC'], true],
            [RulesetHelper::NAME_TO_IDS['taiko'], ['HD', 'DT'], true],
            [RulesetHelper::NAME_TO_IDS['taiko'], ['HD', 'HR'], true],
            [RulesetHelper::NAME_TO_IDS['taiko'], ['HR', 'PF'], true],
            [RulesetHelper::NAME_TO_IDS['taiko'], ['RD', 'SD'], true],

            [RulesetHelper::NAME_TO_IDS['fruits'], ['HD', 'HR'], true],
            [RulesetHelper::NAME_TO_IDS['fruits'], ['HD', 'PF'], true],
            [RulesetHelper::NAME_TO_IDS['fruits'], ['HD', 'SD'], true],
            [RulesetHelper::NAME_TO_IDS['fruits'], ['HD'], true],
            [RulesetHelper::NAME_TO_IDS['fruits'], ['EZ'], true],

            [RulesetHelper::NAME_TO_IDS['mania'], ['DT', 'PF'], true],
            [RulesetHelper::NAME_TO_IDS['mania'], ['NC', 'SD'], true],
            [RulesetHelper::NAME_TO_IDS['mania'], ['6K', 'HD'], true],
            [RulesetHelper::NAME_TO_IDS['mania'], ['4K', 'HT'], true],

            // invalid
            [RulesetHelper::NAME_TO_IDS['osu'], ['5K'], false],
            [RulesetHelper::NAME_TO_IDS['osu'], ['DS'], false],
            [RulesetHelper::NAME_TO_IDS['osu'], ['HD', 'HD'], false],

            [RulesetHelper::NAME_TO_IDS['taiko'], ['AP'], false],

            [RulesetHelper::NAME_TO_IDS['fruits'], ['4K'], false],
            [RulesetHelper::NAME_TO_IDS['fruits'], ['AP'], false],

            [RulesetHelper::NAME_TO_IDS['mania'], ['AP'], false],
        ];
    }

    public static function modComboExclusives()
    {
        return [
            // non-exclusive required mods and no allowed mods
            [RulesetHelper::NAME_TO_IDS['osu'], ['HD', 'NC'], [], true],
            [RulesetHelper::NAME_TO_IDS['mania'], ['DT', 'PF'], [], true],

            // no conflicting exclusive required mods and allowed mods
            [RulesetHelper::NAME_TO_IDS['osu'], ['HD'], ['NC'], true],
            [RulesetHelper::NAME_TO_IDS['mania'], ['DT'], ['PF'], true],

            // conflicting exclusive required mods
            [RulesetHelper::NAME_TO_IDS['osu'], ['HT', 'DT'], [], false],
            [RulesetHelper::NAME_TO_IDS['mania'], ['FI', 'HD'], [], false],

            // allowed mods conflicts with exclusive required mods
            [RulesetHelper::NAME_TO_IDS['osu'], ['HT'], ['DT'], false],
            [RulesetHelper::NAME_TO_IDS['taiko'], ['HT'], ['DT'], false],
        ];
    }
}
