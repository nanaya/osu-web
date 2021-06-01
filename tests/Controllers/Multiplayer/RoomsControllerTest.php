<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Controllers\Multiplayer;

use App\Models\Beatmap;
use App\Models\Beatmapset;
use App\Models\Multiplayer\PlaylistItem;
use App\Models\Multiplayer\Room;
use App\Models\OAuth\Token;
use App\Models\User;
use Tests\TestCase;

class RoomsControllerTest extends TestCase
{
    public function testIndex()
    {
        $room = Room::factory()->create();
        $user = User::factory()->create();

        $this->actAsScopedUser($user, ['*']);

        $this->json('GET', route('api.rooms.index'))->assertSuccessful();
    }

    public function testStore()
    {
        $token = Token::factory()->create(['scopes' => ['*']]);
        $beatmapset = Beatmapset::factory()->create();
        $beatmap = Beatmap::factory()->create(['beatmapset_id' => $beatmapset]);

        $roomsCountInitial = Room::count();
        $playlistItemsCountInitial = PlaylistItem::count();

        $this
            ->actingWithToken($token)
            ->post(route('api.rooms.store'), [
                'ends_at' => now()->addHour(),
                'name' => 'test room',
                'playlist' => [
                    [
                        'beatmap_id' => $beatmap->getKey(),
                        'ruleset_id' => $beatmap->playmode,
                    ],
                ],
            ])->assertSuccessful();

        $this->assertSame($roomsCountInitial + 1, Room::count());
        $this->assertSame($playlistItemsCountInitial + 1, PlaylistItem::count());
    }

    public function testStoreRealtime()
    {
        $token = Token::factory()->create(['scopes' => ['*']]);
        $beatmapset = Beatmapset::factory()->create();
        $beatmap = Beatmap::factory()->create(['beatmapset_id' => $beatmapset]);

        $roomsCountInitial = Room::count();
        $playlistItemsCountInitial = PlaylistItem::count();

        $this
            ->actingWithToken($token)
            ->post(route('api.rooms.store'), [
                'category' => 'realtime',
                'name' => 'test room',
                'playlist' => [
                    [
                        'beatmap_id' => $beatmap->getKey(),
                        'ruleset_id' => $beatmap->playmode,
                    ],
                ],
            ])->assertSuccessful();

        $this->assertSame($roomsCountInitial + 1, Room::count());
        $this->assertSame($playlistItemsCountInitial + 1, PlaylistItem::count());
    }

    public function testStoreRealtimeFailWithTwoPlaylistItems()
    {
        $token = Token::factory()->create(['scopes' => ['*']]);
        $beatmapset = Beatmapset::factory()->create();
        $beatmap1 = Beatmap::factory()->create(['beatmapset_id' => $beatmapset]);
        $beatmap2 = Beatmap::factory()->create(['beatmapset_id' => $beatmapset]);

        $roomsCountInitial = Room::count();
        $playlistItemsCountInitial = PlaylistItem::count();

        $this
            ->actingWithToken($token)
            ->post(route('api.rooms.store'), [
                'category' => 'realtime',
                'name' => 'test room',
                'playlist' => [
                    [
                        'beatmap_id' => $beatmap1->getKey(),
                        'ruleset_id' => $beatmap1->playmode,
                    ],
                    [
                        'beatmap_id' => $beatmap2->getKey(),
                        'ruleset_id' => $beatmap2->playmode,
                    ],
                ],
            ])->assertStatus(422);

        $this->assertSame($roomsCountInitial, Room::count());
        $this->assertSame($playlistItemsCountInitial, PlaylistItem::count());
    }
}
