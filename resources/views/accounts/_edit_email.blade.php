{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
<form
    action="{{ route('account.email') }}"
    class="js-form-error js-form-clear js-account-edit account-edit"
    data-remote
    data-skip-ajax-error-popup="1"
    method="POST"
>
    @csrf
    <input name="_method" value="PUT" type="hidden" />
    <div class="account-edit__section">
        <h2 class="account-edit__section-title">
            {{ osu_trans('accounts.edit.email.title') }}
        </h2>
    </div>

    <div class="account-edit__input-groups">
        @if ($currentUser->lock_email_changes)
            <div class="account-edit__input-group">
                <div class="account-edit-entry account-edit-entry--no-label">
                    <div>{!! osu_trans('accounts.edit.email.locked._', [
                        'accounts' => link_to(
                            "mailto:{$GLOBALS['cfg']['osu']['emails']['account']}",
                            osu_trans('accounts.edit.email.locked.accounts')
                        )
                    ]) !!}</div>
                </div>
            </div>
        @else
            <div class="account-edit__input-group">
                <div class="account-edit-entry js-form-error--field">
                    <input
                        class="account-edit-entry__input"
                        name="user[current_password]"
                        type="password"
                        required
                    >

                    <div class="account-edit-entry__label">
                        {{ osu_trans('accounts.edit.password.current') }}
                    </div>

                    <div class="account-edit-entry__error js-form-error--error"></div>
                </div>
            </div>

            <div class="account-edit__input-group">
                <div class="account-edit-entry js-form-error--field">
                    <input
                        class="account-edit-entry__input js-form-confirmation"
                        name="user[user_email]"
                        required
                    >

                    <div class="account-edit-entry__label">
                        {{ osu_trans('accounts.edit.email.new') }}
                    </div>

                    <div class="account-edit-entry__error js-form-error--error"></div>
                </div>

                <div class="account-edit-entry js-form-error--field">
                    <input
                        class="account-edit-entry__input js-form-confirmation"
                        name="user[user_email_confirmation]"
                        required
                    >

                    <div class="account-edit-entry__label">
                        {{ osu_trans('accounts.edit.email.new_confirmation') }}
                    </div>

                    <div class="account-edit-entry__error js-form-error--error"></div>
                </div>
            </div>

            <div class="account-edit__input-group">
                <div class="account-edit-entry account-edit-entry--no-label">
                    <button class="btn-osu-big btn-osu-big--account-edit" type="submit" data-disable-with="{{ osu_trans('common.buttons.saving') }}">
                        <div class="btn-osu-big__content">
                            <div class="btn-osu-big__left">
                                {{ osu_trans('accounts.update_email.update') }}
                            </div>

                            <div class="btn-osu-big__icon">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </button>

                    @include('accounts._edit_entry_status')
                </div>
            </div>
        @endif
    </div>
</form>
