{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@php
    $user = $currentUser;
@endphp
@if($user === null)
    {!! require_login('store.username_change.require_login._', 'store.username_change.require_login.link_text') !!}
@else
<div class="js-username-change username-change">
    <div style="background-image: url('{{ $user->user_avatar }}');" class="avatar avatar--centered"></div>
    <div>
        <input type="hidden" name="item[product_id]" value="{{ $product->getKey() }}" />
        <input type="hidden" name="item[quantity]" class="js-store-item-quantity" value="1" />
        <input type="hidden" id="username-form-price" name="item[cost]" value="0" />
        <label for="username">{{ osu_trans('store.username_change.label') }}</label>
        <input
            autocomplete="off"
            class="js-username-change-input form-text form-text--username-change"
            id="username"
            name="item[extra_info]"
            placeholder="{{ osu_trans('store.username_change.placeholder') }}"
        />
        <em class="store-text store-text--emphasis">
            <div id="username-check-status">{{ osu_trans('store.username_change.check') }}</div>
        </em>
        <div>{{ osu_trans('store.username_change.current', ['username' => $user->username]) }}</div>
    </div>
    <p class="store-text store-text--price" id="username-check-price"></p>
</div>
@endif
