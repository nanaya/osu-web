<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace Tests\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Tests\TestCase;

class TeamsControllerTest extends TestCase
{
    public function testCreate()
    {
        $user = User::factory()->create();

        $this
            ->actingAsVerified($user)
            ->get(route('teams.create'))
            ->assertSuccessful();
    }

    public function testCreateAsGuest()
    {
        $this->get(route('teams.create'))->assertStatus(401);
    }

    public function testStore()
    {
        $user = User::factory()->create();

        $this->expectCountChange(fn () => Team::count(), 1);
        $this->expectCountChange(fn () => TeamMember::count(), 1);

        $this
            ->actingAsVerified($user)
            ->post(route('teams.store'), ['name' => 'test', 'short_name' => 'test'])
            ->assertRedirect();

        $this->assertNotNull($user->fresh()->teamMembership);
    }

    public function testStoreAlreadyPartOfTeam()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teamMembership()->create(['team_id' => $team->getKey()]);

        $this->expectCountChange(fn () => Team::count(), 0);
        $this->expectCountChange(fn () => TeamMember::count(), 0);

        $this
            ->actingAsVerified($user)
            ->post(route('teams.store'), ['name' => 'test', 'short_name' => 'test'])
            ->assertStatus(403);
    }
}
