{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
<div
    class="js-react--account-legacy-api-key"
    data-data="{{ json_encode(['legacy_api_key' => $legacyApiKeyJson]) }}"
>
    <div class="account-edit">
        <div class="account-edit__section">
            <h2 class="account-edit__section-title">
                {{ osu_trans('accounts.edit.legacy_api_key.title') }}
            </h2>
        </div>

        <div class="account-edit__input-groups">
        </div>
    </div>
</div>
