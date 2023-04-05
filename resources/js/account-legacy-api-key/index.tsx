// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BigButton from 'components/big-button';
import StringWithComponent from 'components/string-with-component';
import LegacyApiKeyJson from 'interfaces/legacy-api-key-json';
import { route } from 'laroute';
import { action, makeObservable, observable, reaction } from 'mobx';
import { disposeOnUnmount, observer } from 'mobx-react';
import * as React from 'react';
import { onError } from 'utils/ajax';
import { trans } from 'utils/lang';
import Form from './form';

export interface Data {
  legacy_api_key: LegacyApiKeyJson | null;
  showing_form?: boolean;
}


interface Props {
  container: HTMLElement;
}

@observer
export default class AccountLegacyApi extends React.Component<Props> {
  @observable private data = JSON.parse(this.props.container.dataset.data ?? '') as Data;
  @observable private revokeXhr: JQuery.jqXHR<void> | null = null;
  @observable private showingKey = false;

  private get isRevoking() {
    return this.revokeXhr != null;
  }

  constructor(props: Props) {
    super(props);

    makeObservable(this);

    disposeOnUnmount(this, reaction(
      () => JSON.stringify(this.data),
      (newDataString) => {
        this.props.container.dataset.data = newDataString;
      },
    ));
  }

  render() {
    return (
      <div className='account-edit'>
        <div className='account-edit__section'>
          <h2 className='account-edit__section-title'>
            {trans('accounts.edit.legacy_api_key.title')}
          </h2>
        </div>

        <div className='account-edit__input-groups'>
          <div className='account-edit__input-group'>
            {this.data.legacy_api_key == null
              ? this.data.showing_form
                ? <Form data={this.data} />
                : this.renderNewButton()
              : this.renderApiKeyView()
            }
          </div>

          <div className='account-edit__input-group'>
            <div className='account-edit-entry account-edit-entry--no-label'>
              <span>
                <StringWithComponent
                  mappings={{ github: (
                    <a href='https://github.com/ppy/osu-api/wiki'>
                      {trans('accounts.edit.legacy_api_key.documentation.github')}
                    </a>
                  ) }}
                  pattern={trans('accounts.edit.legacy_api_key.documentation._')}
                />
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  @action
  private readonly onClickFormShow = () => {
    this.data.showing_form = true;
  };

  @action
  private readonly onClickKeyViewToggle = () => {
    this.showingKey = !this.showingKey;
  };

  @action
  private readonly onClickRevoke = () => {
    if (this.isRevoking) return;

    this.revokeXhr = $.ajax(route('legacy-api-key.destroy'), { method: 'DELETE' })
      .fail(onError)
      .done(action(() => {
        this.data.legacy_api_key = null;
      })).always(action(() => {
        this.revokeXhr = null;
      }));
  };

  private renderApiKeyView() {
    if (this.data.legacy_api_key == null) {
      throw new Error('tried rendering api key view with no key');
    }

    return (
      <>
        <div className='account-edit-entry account-edit-entry--read-only'>
          <div className='account-edit-entry__input'>
            {this.data.legacy_api_key.app_name}
          </div>
          <div className='account-edit-entry__label'>
            {trans('accounts.edit.legacy_api_key.app_name')}
          </div>
        </div>
        <div className='account-edit-entry account-edit-entry--read-only'>
          <div className='account-edit-entry__input'>
            {this.data.legacy_api_key.app_url}
          </div>
          <div className='account-edit-entry__label'>
            {trans('accounts.edit.legacy_api_key.app_url')}
          </div>
        </div>
        <div className='account-edit-entry account-edit-entry--read-only account-edit-entry--wide'>
          <div className='account-edit-entry__input'>
            {this.showingKey ? this.data.legacy_api_key.api_key : '***'}
          </div>
          <div className='account-edit-entry__label'>
            {trans('accounts.edit.legacy_api_key.api_key')}
          </div>
        </div>
        <div className='account-edit-entry account-edit-entry--no-label'>
          <div className='grid-items grid-items--10'>
            <BigButton
              icon={this.showingKey ? 'fas fa-eye-slash' : 'fas fa-eye'}
              modifiers='account-edit'
              props={{
                onClick: this.onClickKeyViewToggle,
              }}
              text={trans(`accounts.edit.legacy_api_key.${this.showingKey ? 'hide' : 'show'}_key`)}
            />

            <BigButton
              disabled={this.isRevoking}
              icon='fas fa-trash'
              modifiers={['account-edit', 'danger']}
              props={{
                onClick: this.onClickRevoke,
              }}
              text={trans('accounts.edit.legacy_api_key.revoke')}
            />
          </div>
        </div>
      </>
    );
  }

  private renderNewButton() {
    return (
      <div className='account-edit-entry account-edit-entry--no-label'>
        <BigButton
          icon='fas fa-plus'
          modifiers='account-edit'
          props={{
            onClick: this.onClickFormShow,
          }}
          text={trans('accounts.edit.legacy_api_key.new')}
        />
      </div>
    );
  }
}
