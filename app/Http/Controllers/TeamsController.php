<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Team;
use App\Transformers\TeamTransformer;
use Symfony\Component\HttpFoundation\Response;

class TeamsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', ['only' => [
            'create',
            'store',
        ]]);
    }

    public function create(): Response
    {
        return ext_view('teams.create');
    }

    public function edit(string $id): Response
    {
        $team = Team::findOrFail($id);
        priv_check('TeamUpdate', $team)->ensureCan();

        return ext_view('teams.edit', compact('team'));
    }

    public function show(string $id): Response
    {
        $team = Team::findOrFail($id);
        $teamJson = json_item($team, new TeamTransformer(), ['members.user.cover']);

        return ext_view('teams.show', compact('teamJson'));
    }

    public function store()
    {
        $team = \DB::transaction(function () {
            $params = get_params(\Request::all(), null, [
                'name',
                'short_name',
            ]);
            $user = \Auth::user();
            if ($user->teamMembership()->exists()) {
                abort(403, 'already member of a team');
            }
            $team = (new Team($params));
            $team->saveOrExplode();
            $team->members()->create([
                'user_id' => $user->getKey(),
                'is_owner' => true,
            ]);

            return $team;
        });

        return ujs_redirect(route('teams.show', $team));
    }

    public function update(string $id): Response
    {
        $team = Team::findOrFail($id);
        priv_check('TeamUpdate', $team)->ensureCan();
        $params = get_params(\Request::all(), 'team', [
            'description',
            'header:file',
            'header_remove:bool',
            'is_open:bool',
            'logo:file',
            'logo_remove:bool',
        ]);

        $team->fill($params)->saveOrExplode();

        return ext_view('layout.ujs-reload', [], 'js');
    }
}
