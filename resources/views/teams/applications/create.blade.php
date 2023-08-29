{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <div class="osu-page osu-page--generic">
        <h1>
            {{ osu_trans('teams.applications.create.title', ['team' => $team->name]) }}
        </h1>
        <form
            action="{{ route('teams.applications.store', ['team' => $team->getKey()]) }}"
            method="POST"
        >
            @csrf
            <div>
                <label>
                    {{ osu_trans('teams.applications.create.message_label') }}
                    <textarea name="message"></textarea>
                </label>
            </div>
            <button class="btn-osu-big">
                {{ osu_trans('teams.applications.create.submit') }}
            </button>
        </form>
    </div>
@endsection
