{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <div class="osu-page osu-page--generic">
        <h1>
            {{ $team->name }}
        </h1>
        <ul>
            @foreach ($team->members as $member)
                <li>
                    {{ $member->user->username }}
                    <button class="btn-osu-big">
                        <span class="fas fa-trash"></span>
                    </button>
            @endforeach
        </ul>

        <h2>Applications</h2>
        @if (count($team->applications) > 0)
            <ul>
                @foreach ($team->applications as $application)
                    <li>
                        {{ $application->user->username }}
                        <ul>
                            <li>Created: {{ $application->created_at }}
                            <li>Status: {{ $application->is_new === true ? 'open' : 'rejected' }}
                        </ul>
                @endforeach
            </ul>
        @else
            no invites
        @endif
        <h3>Invite User</h3>
            <form
                action="{{ route('teams.invites.store', ['team' => $team->getKey()]) }}"
                method="POST"
                data-remote="1"
            >
                @csrf
                <input name="username" />
                <button class="btn-osu-big">
                    {{ osu_trans('common.buttons.save') }}
                </button>
            </form>
        </h3>
    </div>
@endsection
