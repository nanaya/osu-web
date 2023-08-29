{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master')

@section('content')
    @include('layout._page_header_v4')

    <div class="osu-page osu-page--team-create">
        <form method="POST" action="{{ route('teams.store') }}" class="u-contents">
            <div class="page-extra">
                <h2 class="title title--page-extra-small">
                    Let's set up a new team
                </h2>

                <div class="team-create-form">
                    @csrf

                    <div class="team-create-form__row">
                        <label class="input-container">
                            <span class="input-container__label">
                                Team Name
                            </span>
                            <input class="input-text" name="name" />
                        </label>
                        <div class="team-create-form__help">
                            Description what
                        </div>
                    </div>

                    <div class="team-create-form__row">
                        <label class="input-container">
                            <span class="input-container__label">
                                Short Name
                            </span>
                            <input class="input-text" name="short_name" />
                        </label>
                        <div class="team-create-form__help">
                            Description what
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-extra">
                <button class="btn-osu-big btn-osu-big--rounded-thin-wide">
                    {{ osu_trans('common.buttons.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection
