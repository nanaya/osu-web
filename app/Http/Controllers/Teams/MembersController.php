<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Symfony\Component\HttpFoundation\Response;

class MembersController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function index(string $teamId): Response
    {
        $team = Team::findOrFail($teamId);

        priv_check('TeamInviteCreate', $team)->ensureCan();

        return ext_view('teams.members.index', compact('team'));
    }
}
