{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master', [
    'titlePrepend' => $teamJson['name'],
])

@section('content')
    <div
        class="js-react--team u-contents"
        data-props="{{ json_encode(['team' => $teamJson]) }}"
    ></div>
    @include('layout._react_js', ['src' => 'js/team.js'])
@endsection
