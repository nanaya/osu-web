{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@php
@endphp
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <div class="osu-page osu-page--generic">
        <h1>Team edit</h1>

        <form
            data-remote="1"
            method="POST"
            action="{{ route('teams.update', ['team' => $team->getKey()]) }}"
        >
            @csrf
            <input name="_method" value="PUT" type="hidden" />
            <p>
            <textarea
                name="team[description]"
                style="background-color: hsl(var(--hsl-b5))"
                autofocus
            >{{ $team->description }}</textarea>
            </p>

            <button class="btn-osu-big">
                {{ osu_trans('common.buttons.save') }}
            </button>
        </form>

        <p>
            <a href="{{ route('teams.show', ['team' => $team]) }}">
                back
            </a>
        </p>

        <p>
            <h1>Header</h1>
            <form action="{{ route('teams.update', ['team' => $team]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input name="_method" value="PUT" type="hidden" />
                <input type="file" name="team[header]">
                <button class="btn-osu-big">
                    {{ osu_trans('common.buttons.save') }}
                </button>
            </form>
        </p>

        <p>
            <h1>Logo</h1>
            <form action="{{ route('teams.update', ['team' => $team]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input name="_method" value="PUT" type="hidden" />
                <input type="file" name="team[logo]">
                <button class="btn-osu-big">
                    {{ osu_trans('common.buttons.save') }}
                </button>
            </form>
        </p>
    </div>
@endsection
