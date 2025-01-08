<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace Tests\Models\Multiplayer;

use App\Models\Multiplayer\PlaylistItem;
use App\Models\Multiplayer\Room;
use App\Models\Multiplayer\UserScoreAggregate;
use App\Models\User;
use App\Transformers\Multiplayer\UserScoreAggregateTransformer;
use Tests\TestCase;

class UserScoreAggregateTest extends TestCase
{
    private Room $room;

    public function testAddingHigherScore(): void
    {
        $user = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();

        // first play
        $scoreLink = static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.5,
            'passed' => true,
            'total_score' => 10,
        ]);

        $agg = UserScoreAggregate::new($user, $this->room);
        $this->assertSame(1, $agg->completed);
        $this->assertSame(0.5, $agg->accuracy);
        $this->assertSame(10, $agg->total_score);
        $this->assertSame($scoreLink->getKey(), $agg->last_score_id);

        // second, higher score play
        $scoreLink2 = static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 1,
            'passed' => true,
            'total_score' => 100,
        ]);

        $agg->refresh();
        $this->assertSame(1, $agg->completed);
        $this->assertSame(1.0, $agg->accuracy);
        $this->assertSame(100, $agg->total_score);
        $this->assertSame($scoreLink2->getKey(), $agg->last_score_id);
    }

    public function testAddingLowerScore(): void
    {
        $user = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();

        // first play
        $scoreLink = static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.5,
            'passed' => true,
            'total_score' => 10,
        ]);

        $agg = UserScoreAggregate::new($user, $this->room);
        $this->assertSame(1, $agg->completed);
        $this->assertSame(0.5, $agg->accuracy);
        $this->assertSame(10, $agg->total_score);
        $this->assertSame($scoreLink->getKey(), $agg->last_score_id);

        // second, lower score play
        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 1,
            'passed' => true,
            'total_score' => 1,
        ]);

        $agg->refresh();
        $this->assertSame(1, $agg->completed);
        $this->assertSame(0.5, $agg->accuracy);
        $this->assertSame(10, $agg->total_score);
        $this->assertSame($scoreLink->getKey(), $agg->last_score_id);
    }

    public function testAddingEqualScore(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();

        // first user sets play
        $firstUserPlay = static::roomAddPlay($firstUser, $playlistItem, [
            'accuracy' => 0.5,
            'passed' => true,
            'total_score' => 10,
        ]);

        $firstUserAgg = UserScoreAggregate::new($firstUser, $this->room);
        $this->assertSame(1, $firstUserAgg->completed);
        $this->assertSame(0.5, $firstUserAgg->accuracy);
        $this->assertSame(10, $firstUserAgg->total_score);
        $this->assertSame(1, $firstUserAgg->userRank());
        $this->assertSame($firstUserPlay->getKey(), $firstUserAgg->last_score_id);

        // second user sets play with same total, so they get second place due to being late
        $secondUserPlay = static::roomAddPlay($secondUser, $playlistItem, [
            'accuracy' => 0.5,
            'passed' => true,
            'total_score' => 10,
        ]);

        $secondUserAgg = UserScoreAggregate::new($secondUser, $this->room);
        $this->assertSame(1, $secondUserAgg->completed);
        $this->assertSame(0.5, $secondUserAgg->accuracy);
        $this->assertSame(10, $secondUserAgg->total_score);
        $this->assertSame(2, $secondUserAgg->userRank());
        $this->assertSame($secondUserPlay->getKey(), $secondUserAgg->last_score_id);

        // first user sets play with same total again, but their rank should not move now
        static::roomAddPlay($firstUser, $playlistItem, [
            'accuracy' => 0.5,
            'passed' => true,
            'total_score' => 10,
        ]);

        $firstUserAgg->refresh();
        $this->assertSame(1, $firstUserAgg->completed);
        $this->assertSame(0.5, $firstUserAgg->accuracy);
        $this->assertSame(10, $firstUserAgg->total_score);
        $this->assertSame(1, $firstUserAgg->userRank());
        $this->assertSame($firstUserPlay->getKey(), $firstUserAgg->last_score_id);

        $secondUserAgg->refresh();
        $this->assertSame(2, $secondUserAgg->userRank());
    }

    public function testAddingMultiplePlaylistItems(): void
    {
        $user = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();
        $playlistItem2 = $this->createPlaylistItem();

        // first playlist item
        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.5,
            'passed' => true,
            'total_score' => 10,
        ]);

        $agg = UserScoreAggregate::new($user, $this->room);
        $this->assertSame(1, $agg->completed);
        $this->assertSame(0.5, $agg->accuracy);
        $this->assertSame(0.5, $agg->averageAccuracy());
        $this->assertSame(10, $agg->total_score);

        // second playlist item
        $scoreLink = static::roomAddPlay($user, $playlistItem2, [
            'accuracy' => 1,
            'passed' => true,
            'total_score' => 100,
        ]);

        $agg->refresh();
        $this->assertSame(2, $agg->completed);
        $this->assertSame(1.5, $agg->accuracy);
        $this->assertSame(0.75, $agg->averageAccuracy());
        $this->assertSame(110, $agg->total_score);
        $this->assertSame($scoreLink->getKey(), $agg->last_score_id);
    }

    public function testStartingPlayIncreasesAttempts(): void
    {
        $user = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();

        static::roomStartPlay($user, $playlistItem);
        $agg = UserScoreAggregate::new($user, $this->room);

        $this->assertSame(1, $agg->attempts);
        $this->assertSame(0, $agg->completed);
    }

    public function testFailedScoresAreAttemptsOnly(): void
    {
        $user = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();

        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.1,
            'passed' => false,
            'total_score' => 10,
        ]);

        $playlistItem2 = $this->createPlaylistItem();
        static::roomAddPlay($user, $playlistItem2, [
            'accuracy' => 1,
            'passed' => true,
            'total_score' => 1,
        ]);

        $agg = UserScoreAggregate::new($user, $this->room);

        $this->assertSame(2, $agg->attempts);
        $this->assertSame(1, $agg->completed);
        $this->assertSame(1.0, $agg->averageAccuracy());
        $this->assertSame(1, $agg->total_score);
    }

    public function testPassedScoresIncrementsCompletedCount(): void
    {
        $user = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();

        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 1,
            'passed' => true,
            'total_score' => 1,
        ]);

        $agg = UserScoreAggregate::new($user, $this->room);

        $this->assertSame(1, $agg->completed);
        $this->assertSame(1, $agg->total_score);
    }

    public function testPassedScoresAreAveragedInTransformer(): void
    {
        $user = User::factory()->create();
        $playlistItem = $this->createPlaylistItem();
        $playlistItem2 = $this->createPlaylistItem();

        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.1,
            'passed' => false,
            'total_score' => 1,
        ]);

        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.3,
            'passed' => false,
            'total_score' => 1,
        ]);

        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.5,
            'passed' => true,
            'total_score' => 1,
        ]);

        static::roomAddPlay($user, $playlistItem2, [
            'accuracy' => 0.8,
            'passed' => true,
            'total_score' => 1,
        ]);

        $agg = UserScoreAggregate::new($user, $this->room);

        $result = json_item($agg, new UserScoreAggregateTransformer());

        $this->assertSame(0.65, $result['accuracy']);
    }

    public function testRecalculate(): void
    {
        $playlistItem = $this->createPlaylistItem();
        $user = User::factory()->create();
        static::roomAddPlay($user, $playlistItem, [
            'accuracy' => 0.3,
            'passed' => true,
            'total_score' => 1,
        ]);
        $agg = UserScoreAggregate::new($user, $this->room);
        $agg->recalculate();
        $agg->refresh();

        $this->assertSame(1, $agg->total_score);
        $this->assertSame(1, $agg->attempts);
        $this->assertSame(0.3, $agg->accuracy);
        $this->assertSame(1, $agg->completed);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = Room::factory()->create();
    }

    private function createPlaylistItem(): PlaylistItem
    {
        return PlaylistItem::factory()->create([
            'owner_id' => $this->room->host,
            'room_id' => $this->room,
        ]);
    }
}
