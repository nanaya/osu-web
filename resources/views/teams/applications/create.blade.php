{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@php
    $userTransformer = new App\Transformers\UserCompactTransformer();
    $headerUrl = $team->header()->url();

    $currentUser = Auth::user();
@endphp

@extends('master', [
    'titlePrepend' => $team->name,
])

@section('content')
    @include('layout._page_header_v4', ['params' => [
        'theme' => 'team',
        'backgroundImage' => $headerUrl,
    ]])

    <form
        class="osu-page osu-page--generic-compact"
        action="{{ route('teams.applications.store', ['team' => $team]) }}"
        method="POST"
    >
        @csrf
        <div class="user-profile-pages user-profile-pages--no-tabs">
            <div class="page-extra u-fancy-scrollbar">
                <h2 class="title title--page-extra-small title--page-extra-small-top">
                    {{ osu_trans('teams.applications.create.title') }}
                </h2>

                <p>
                    {{ osu_trans('teams.applications.create.message', ['name' => $team->name]) }}
                </p>

                <div class="team-settings">
                    <div class="team-settings__item">
                        <label class="input-container">
                            <span class="input-container__label">
                                {{ osu_trans('teams.applications.create.form.message') }}
                            </span>
                            <textarea name="message" class="input-text">{{ $application->message }}</textarea>
                        </label>
                        <span class="team-settings__help">
                            {{ osu_trans('teams.applications.create.form.message_help') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="page-extra">
                <div class="team-settings">
                    <div class="team-settings__item team-settings__item--buttons">
                        <div>
                            <a
                                class="btn-osu-big btn-osu-big--rounded-thin"
                                href="{{ route('teams.show', ['team' => $team]) }}"
                            >
                                {{ osu_trans('common.buttons.cancel') }}
                            </a>
                        </div>

                        <div>
                            <button class="btn-osu-big btn-osu-big--rounded-thin">
                                {{ osu_trans('common.buttons.submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
