<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Controllers\Teams;

use App\Models\Team;
use App\Models\User;
use Tests\TestCase;

class ApplicationsControllerTest extends TestCase
{
    public function testAccept()
    {
        $team = Team::factory()->create();
        $owner = User::factory()->create();
        $team->members()->create([
            'is_owner' => true,
            'user_id' => $owner->getKey(),
        ]);
        $application = $team->applications()->create(['user_id' => User::factory()->create()->getKey()]);

        $this->expectCountChange(fn () => $team->applications()->where('is_new', true)->count(), -1);
        $this->expectCountChange(fn () => $team->members()->count(), 1);
        $this
            ->actingAsVerified($owner)
            ->post(route('teams.applications.accept', ['team' => $team->getKey(), 'application' => $application->getKey()]))
            ->assertStatus(200);
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $this->expectCountChange(fn () => $team->applications()->where('is_new', true)->count(), 1);

        $this
            ->actingAsVerified($user)
            ->post(route('teams.applications.store', ['team' => $team->getKey()]), ['message' => 'hello'])
            ->assertStatus(302);
    }

    public function testStoreAlreadyApplying()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $team->applications()->create(['user_id' => $user->getKey()]);
        $otherTeam = Team::factory()->create();

        $this->expectCountChange(fn () => $otherTeam->applications()->count(), 0);

        $this
            ->actingAsVerified($user)
            ->post(route('teams.applications.store', ['team' => $otherTeam->getKey()]), ['message' => 'hello'])
            ->assertStatus(403);
    }

    public function testStoreAlreadyTeamMember()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->create([
            'user_id' => $user->getKey(),
        ]);

        $this->expectCountChange(fn () => $team->applications()->count(), 0);

        $this
            ->actingAsVerified($user)
            ->post(route('teams.applications.store', ['team' => $team->getKey()]), ['message' => 'hello'])
            ->assertStatus(403);
    }

    public function testStoreAlreadyOtherTeamMember()
    {
        $user = User::factory()->create();
        $otherTeam = Team::factory()->create();
        $otherTeam->members()->create([
            'user_id' => $user->getKey(),
        ]);
        $team = Team::factory()->create();

        $this->expectCountChange(fn () => $team->applications()->count(), 0);
        $this->expectCountChange(fn () => $otherTeam->applications()->count(), 0);

        $this
            ->actingAsVerified($user)
            ->post(route('teams.applications.store', ['team' => $team->getKey()]), ['message' => 'hello'])
            ->assertStatus(403);
    }
}
