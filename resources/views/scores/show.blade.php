{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@php
    $username = $score->user === null || $score->user->trashed() ? osu_trans('users.deleted') : $score->user->username;
    $title = osu_trans('scores.show.title', [
        'username' => $username,
        'title' => $score->beatmap->beatmapset->getDisplayTitle($currentUser),
        'version' => $score->beatmap->version,
    ]);
@endphp
@extends('master', [
    'titlePrepend' => $title,
])

@section('content')
    <div class="js-react--scores-show u-contents"></div>

    <script id="json-show" type="application/json">
        {!! json_encode($scoreJson) !!}
    </script>

    <script id="json-raw" type="application/json">
        {!! json_encode($score->getAttributes()) !!}
    </script>

    @include('layout._react_js', ['src' => 'js/scores-show.js'])
@endsection
