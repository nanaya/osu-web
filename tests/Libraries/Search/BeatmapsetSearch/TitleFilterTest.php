<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace Tests\Libraries\Search\BeatmapsetSearch;

use App\Models\Beatmapset;

class TitleFilterTest extends TestCase
{
    public static function dataProvider(): array
    {
        return [
            [['q' => 'best'], [0, 4, 3, 2, 1]],
            [['q' => 'best beatmap'], [3, 2, 1, 0, 4]],
            [['q' => '"best beatmap"'], [3, 2, 1]],
            [['q' => '-best'], []],
            [['q' => '-best -beatmap'], []],
            [['q' => '-"best beatmap"'], [4, 0]],

            [['q' => 'title=best'], [0, 3, 2, 1]],
            [['q' => 'title="best beatmap"'], [3, 2, 1]],
            [['q' => 'title="the beatmap"'], [1, 2]],
            [['q' => 'title=""best beatmap""'], [3, 2, 1]],
            [['q' => 'title=""the beatmap""'], []],
        ];
    }

    public static function setUpBeforeClass(): void
    {
        static::withDbAccess(function () {
            $factory = Beatmapset::factory()->ranked()->withBeatmaps()->state([
                'artist' => 'an artist',
                'creator' => 'a creator',
                'favourite_count' => 0,
                'tags' => '',
                'title' => 'a title',
            ]);
            static::$beatmapsets = [
                $factory->create(['title' => 'best']),
                $factory->create(['title' => 'the best beatmap']),
                $factory->create(['title' => 'the best beatmap', 'title_unicode' => 'ダ best beatmap']),
                $factory->create(['title' => 'best beatmap']),
                $factory->create(['artist' => 'best artist']),
            ];
        });

        parent::setUpBeforeClass();
    }
}
