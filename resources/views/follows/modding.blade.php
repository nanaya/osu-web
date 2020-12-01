{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master')

@section('content')
    <div class="js-react--follows-modding osu-layout osu-layout--full"></div>

    <script id="json-follows-modding" type="application/json">
        {!! json_encode($followsJson) !!}
    </script>

    @include('layout._extra_js', ['src' => 'js/react/follows-modding.js'])
@endsection
