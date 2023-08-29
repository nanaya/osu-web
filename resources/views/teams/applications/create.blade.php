{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <div class="osu-page osu-page--generic">
        <h1>
            Create invite for {{ $team->name }}
        </h1>
        <form
            action="{{ route('teams.invites.store', ['team' => $team->getKey()]) }}"
            method="POST"
        >
            @csrf
            <p>
            <input name="username" />
            </p>
            <button class="btn-osu-big">
                {{ osu_trans('common.buttons.save') }}
            </button>
        </form>
    </div>
@endsection
