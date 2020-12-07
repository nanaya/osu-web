{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
<div class="page-tabs page-tabs--follows">
    @foreach (['forum-topic', 'modding'] as $menuType)
        <a
            href="{{ route('follows.index', ['type' => $menuType]) }}"
            class="page-tabs__tab {{ $type === $menuType ? 'page-tabs__tab--active' : '' }}"
        >
            {{ trans('follows.'.str_replace('-', '_', $menuType).'.title') }}
        </a>
    @endforeach
</div>
