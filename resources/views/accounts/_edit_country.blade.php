{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@php
    $countryChangeTarget = App\Libraries\User\CountryChangeTarget::get($currentUser);
@endphp
<div class="account-edit-entry account-edit-entry--read-only">
    <div class="account-edit-entry__label account-edit-entry__label--top-pinned">
        {{ osu_trans('accounts.edit.profile.country') }}
    </div>
    <div class="account-edit-entry__group">
        <p>
            @include('objects._flag_country', [
                'countryCode' => $currentUser->country_acronym,
                'countryName' => null,
                'modifiers' => 'wiki',
            ])
            {{ $currentUser->country->name }}
        </p>
        @if ($countryChangeTarget !== null)
            <p>
                {!! osu_trans('accounts.edit.profile.country_change._', [
                    'update_link' => link_to(
                        route('account.country', ['country_acronym' => $countryChangeTarget]),
                        osu_trans('accounts.edit.profile.country_change.update_link', [
                            'country' => app('countries')->byCode($countryChangeTarget)->name,
                        ]),
                        [
                            'data-confirm' => osu_trans('common.confirmation'),
                            'data-method' => 'PUT',
                            'data-remote' => '1',
                        ]
                    ),
                ]) !!}
            </p>
        @endif
    </div>
</div>
