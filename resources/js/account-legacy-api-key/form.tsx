// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BigButton from 'components/big-button';
import LegacyApiKeyJson from 'interfaces/legacy-api-key-json';
import { route } from 'laroute';
import { action, makeObservable, observable, runInAction } from 'mobx';
import { observer } from 'mobx-react';
import * as React from 'react';
import { onError } from 'utils/ajax';
import { trans } from 'utils/lang';
import { Data } from './index';

interface Props {
  data: Data;
}

@observer
export default class Form extends React.Component<Props> {
  @observable private appName = '';
  private appNameRef = React.createRef<HTMLInputElement>();
  @observable private appUrl = '';
  @observable private xhr: JQuery.jqXHR<LegacyApiKeyJson> | null = null;

  private get isBusy() {
    return this.xhr != null;
  }

  constructor(props: Props) {
    super(props);

    makeObservable(this);
  }

  componentDidMount() {
    this.appNameRef.current?.focus();
  }

  render() {
    return (
      <form
        className='u-contents'
        data-loading-overlay='0'
        onSubmit={this.onSubmit}
      >
        <label className='account-edit-entry'>
          <input
            ref={this.appNameRef}
            className='account-edit-entry__input'
            maxLength={100}
            name='app_name'
            onChange={this.onChangeAppName}
            required
            value={this.appName}
          />
          <div className='account-edit-entry__label'>
            {trans('accounts.edit.legacy_api_key.app_name')}
          </div>
        </label>
        <label className='account-edit-entry'>
          <input
            className='account-edit-entry__input'
            maxLength={512}
            name='app_url'
            onChange={this.onChangeAppUrl}
            type='url'
            required
            value={this.appUrl}
          />
          <div className='account-edit-entry__label'>
            {trans('accounts.edit.legacy_api_key.app_url')}
          </div>
        </label>
        <div className='account-edit-entry account-edit-entry--no-label'>
          <div className='grid-items grid-items--10'>
            <BigButton
              icon='fas fa-times'
              modifiers='account-edit'
              props={{
                onClick: this.onClickFormCancel,
              }}
              text={trans('common.buttons.cancel')}
            />
            <BigButton
              icon='fas fa-save'
              isSubmit
              modifiers='account-edit'
              text={trans('accounts.edit.legacy_api_key.create')}
            />
          </div>
        </div>
      </form>
    );
  }

  @action
  private readonly onChangeAppName = (event: React.ChangeEvent<HTMLInputElement>) => {
    this.appName = event.currentTarget.value;
  };

  @action
  private readonly onChangeAppUrl = (event: React.ChangeEvent<HTMLInputElement>) => {
    this.appUrl = event.currentTarget.value;
  };

  @action
  private readonly onClickFormCancel = () => {
    if ((this.appName !== '' || this.appUrl !== '') && !confirm(trans('common.confirmation_unsaved'))) {
      return;
    }

    this.props.data.showing_form = false;
  };

  @action
  private readonly onSubmit = (e: React.SyntheticEvent) => {
    e.preventDefault();

    if (this.isBusy) return;

    this.xhr = $.ajax(route('legacy-api-key.store'), {
      data: {
        legacy_api_key: {
          app_name: this.appName,
          app_url: this.appUrl,
        },
      },
      method: 'POST',
    });

    this.xhr.done((data) => runInAction(() => {
      this.props.data.legacy_api_key = data;
      this.props.data.showing_form = false;
    })).fail(
      onError,
    ).always(action(() => {
      this.xhr = null;
    }));
  };
}
