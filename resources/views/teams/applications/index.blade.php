{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <div class="osu-page osu-page--generic">
        <h1>
            Invites on {{ $team->name }}
        </h1>
        <ul>
            @foreach ($team->invites as $invite)
                <li>{{ $invite->invitee->username }}
            @endforeach
        </ul>
    </div>
@endsection
