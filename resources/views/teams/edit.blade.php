{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@php
@endphp
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <form
        method="POST"
        action="{{ route('teams.update', ['team' => $team->getKey()]) }}"
        enctype="multipart/form-data"
        class="osu-page osu-page--generic-compact"
        data-reload-on-success="1"
    >
        @csrf
        <input name="_method" value="PUT" type="hidden" />

        <div class="user-profile-pages user-profile-pages--no-tabs">
            <div class="page-extra">
                <h2 class="title title--page-extra-small title--page-extra-small-top">
                    {{ osu_trans('teams.edit.settings.title') }}
                </h2>

                <div class="team-settings">
                    <div class="team-settings__item">
                        <label class="input-container">
                            <span class="input-container__label">
                                {{ osu_trans('teams.edit.settings.url') }}
                            </span>
                            <input
                                name="team[url]"
                                class="input-text"
                                value="{{ $team->url }}"
                            />
                        </label>
                    </div>

                    <div class="team-settings__item">
                        <label class="input-container input-container--select">
                            <span class="input-container__label">
                                {{ osu_trans('teams.edit.settings.default_ruleset') }}
                            </span>
                            <select
                                name="team[default_ruleset_id]"
                                class="input-text"
                                value="{{ $team->default_ruleset_id }}"
                            >
                                @foreach (App\Models\Beatmap::MODES as $rulesetName => $rulesetId)
                                    <option
                                        value="{{ $rulesetId }}"
                                        @if ($rulesetId === $team->default_ruleset_id)
                                            selected
                                        @endif
                                    >
                                        {{ osu_trans("beatmaps.mode.{$rulesetName}") }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <span class="team-settings__help">
                            {{ osu_trans('teams.edit.settings.default_ruleset_help') }}
                        </span>
                    </div>

                    <div class="team-settings__item">
                        <label class="input-container input-container--select">
                            <span class="input-container__label">
                                {{ osu_trans('teams.edit.settings.application') }}
                            </span>
                            @php
                                $currentIsOpen = $team->is_open;
                            @endphp
                            <select
                                name="team[is_open]"
                                class="input-text"
                                value="{{ $currentIsOpen }}"
                            >
                                @foreach ([1, 0] as $isOpen)
                                    <option
                                        value="{{ $isOpen }}"
                                        @if ($currentIsOpen === $isOpen)
                                            selected
                                        @endif
                                    >
                                        {{ osu_trans("teams.edit.settings.application_state.state_{$isOpen}") }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <span class="team-settings__help">
                            {{ osu_trans('teams.edit.settings.application_help') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="page-extra">
                <h2 class="title title--page-extra-small title--page-extra-small-top">
                    {{ osu_trans('teams.edit.header.title') }}
                </h2>
                <div class="team-settings">
                    <div class="team-settings__item team-settings__item--image">
                        <img
                            class="team-settings__image"
                            src="{{ $team->header()->url() }}"
                        >
                        <label class="input-container">
                            <span class="input-container__label">
                                {{ osu_trans('teams.edit.header.label') }}
                            </span>
                            <input class="input-text" type="file" name="team[header]">
                        </label>
                    </div>
                </div>
            </div>

            <div class="page-extra">
                <h2 class="title title--page-extra-small title--page-extra-small-top">
                    {{ osu_trans('teams.edit.logo.title') }}
                </h2>
                <div class="team-settings">
                    <div class="team-settings__item team-settings__item--image">
                        <img
                            class="team-settings__image"
                            src="{{ $team->logo()->url() }}"
                        >
                        <label class="input-container">
                            <span class="input-container__label">
                                {{ osu_trans('teams.edit.logo.label') }}
                            </span>
                            <input class="input-text" type="file" name="team[logo]">
                        </label>
                    </div>
                </div>
            </div>

            <div class="page-extra">
                <h2 class="title title--page-extra-small title--page-extra-small-top">
                    {{ osu_trans('teams.edit.description.title') }}
                </h2>

                <div class="team-settings">
                    <div class="team-settings__item team-settings__item--description">
                        <label class="input-container">
                            <span class="input-container__label">
                                {{ osu_trans('teams.edit.description.label') }}
                            </span>
                            <div class="input-text input-text--bbcode">
                                <textarea
                                    name="team[description]"
                                    class="input-text__bbcode-textarea js-post-preview--auto js-bbcode-body"
                                >{{ $team->description }}</textarea>
                                @include('forum._post_toolbar')
                            </div>
                        </label>

                        <div class="team-settings__description-preview u-fancy-scrollbar">
                            <div class="team-settings-description-preview js-post-preview--preview">
                                {!! $team->descriptionHtml() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-extra-container">
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
                                    {{ osu_trans('common.buttons.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
