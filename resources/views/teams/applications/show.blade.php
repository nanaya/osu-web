{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <div class="osu-page osu-page--generic">
        <h1>
            Invite of {{ $invite->team->name }} for {{ $invite->invitee->username }}
        </h1>
        <form
            action="{{ route('teams.invites.update', ['team' => $invite->team_id, 'invite' => $invite->getKey()]) }}"
            data-remote="1"
            method="POST"
        >
            @csrf
            <input name="_method" value="PUT" type="hidden" />
            <button name="accept" value="1" class="btn-osu-big">
                accept
            </button>
            <button name="accept" value="0" class="btn-osu-big">
                reject
            </button>
        </form>
    </div>
@endsection
