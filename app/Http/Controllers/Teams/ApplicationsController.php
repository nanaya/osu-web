<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Symfony\Component\HttpFoundation\Response;

class ApplicationsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function accept(string $teamId, string $id): array
    {
        \DB::transaction(function () use ($id, $teamId) {
            $team = Team::findOrFail($teamId);
            $application = $team->applications()->where('is_new', true)->findOrFail($id);

            priv_check('TeamApplicationAccept', $application)->ensureCan();

            $application->update(['is_new' => null]);
            $team->members()->create(['user_id' => $application->user_id]);
        });

        return []; // wat
    }

    public function create(string $teamId): Response
    {
        $team = Team::findOrFail($teamId);
        priv_check('TeamApply', $team)->ensureCan();

        return ext_view('teams.applications.create', compact('team'));
    }

    public function show(string $teamId, string $id): Response
    {
        $invite = \Auth::user()->teamInvites()->where('team_id', $teamId)->findOrFail($id);

        return ext_view('teams.invites.show', compact('invite'));
    }

    public function store(string $teamId): Response
    {
        $team = Team::findOrFail($teamId);

        priv_check('TeamApply', $team)->ensureCan();

        $team->applications()->createOrFirst(
            ['user_id' => \Auth::id()],
            ['message' => presence(get_string(request('message'))) ?? ''],
        );

        return ujs_redirect(route('teams.show', $team));
    }
}
