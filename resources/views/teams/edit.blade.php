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

            <label class="input-container">
                <span class="input-container__label">
                    Description
                </span>
                <textarea
                    name="team[description]"
                    class="input-text"
                    autofocus
                >{{ $team->description }}</textarea>
            </label>

            <button class="btn-osu-big btn-osu-big--rounded-thin">
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

                <label class="input-container">
                    <span class="input-container__label">
                        Header Image
                    </span>
                    <input class="input-text" type="file" name="team[header]">
                </label>

                <button class="btn-osu-big btn-osu-big--rounded-thin">
                    {{ osu_trans('common.buttons.save') }}
                </button>
            </form>
        </p>

        <p>
            <h1>Logo</h1>
            <form action="{{ route('teams.update', ['team' => $team]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input name="_method" value="PUT" type="hidden" />

                <label class="input-container">
                    <span class="input-container__label">
                        Logo Image
                    </span>
                    <input class="input-text" type="file" name="team[logo]">
                </label>

                <button class="btn-osu-big btn-osu-big--rounded-thin">
                    {{ osu_trans('common.buttons.save') }}
                </button>
            </form>
        </p>
    </div>
@endsection
