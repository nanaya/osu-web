<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Models\UserStatistics;

use App\Exceptions\ClassNotFoundException;
use App\Libraries\RulesetHelper;
use App\Models\UserStatistics\Model;
use Tests\TestCase;

class ModelTest extends TestCase
{
    /**
     * @dataProvider validModes
     */
    public function testGetClass($mode, $variant)
    {
        $class = Model::getClass($mode, $variant);
        $this->assertInstanceOf(Model::class, new $class());
    }

    /**
     * @dataProvider invalidModes
     */
    public function testGetClassByThrowsExceptionIfModeDoesNotExist($mode, $variant)
    {
        $this->expectException(ClassNotFoundException::class);
        Model::getClass($mode, $variant);
    }

    public static function invalidModes()
    {
        return [
            ['does', null],
            ['not exist', null],
            ['not_real', null],
            ['best\\_osu', null],
            ['osu', '4k'],
        ];
    }

    public static function validModes()
    {
        $modes = [];

        foreach (RulesetHelper::NAME_TO_IDS as $mode => $_modeInt) {
            $modes[] = [$mode, null];

            foreach (RulesetHelper::VARIANTS[$mode] ?? [] as $variant) {
                $modes[] = [$mode, $variant];
            }
        }

        return $modes;
    }
}
