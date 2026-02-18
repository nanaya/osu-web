// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import BlockButton from 'components/block-button';
import PopupMenu from 'components/popup-menu';
import PopupMenuState from 'components/popup-menu-state';
import { ReportReportable } from 'components/report-reportable';
import UserJson from 'interfaces/user-json';
import core from 'osu-core-singleton';
import * as React from 'react';
import { trans } from 'utils/lang';
import { giftSupporterTagUrl } from 'utils/url';

interface Props {
  user: UserJson;
}

export function showExtraMenu(user: UserJson) {
  return core.currentUser != null && core.currentUser.id !== user.id;
}

export default function ExtraMenu(props: Props) {
  const popupMenuState = React.useMemo(() => new PopupMenuState(), []);

  return (
    <div
      className='btn-circle btn-circle--page-toggle btn-circle--page-toggle-detail'
      title={trans('common.buttons.show_more_options')}
    >
      <PopupMenu state={popupMenuState}>
        <div className='simple-menu'>
          <a
            className='simple-menu__item'
            href={giftSupporterTagUrl(props.user)}
            onClick={popupMenuState.dismiss}
          >
            <span className='fas fa-gift' />
            {` ${trans('users.card.gift_supporter')}`}
          </a>

          <BlockButton
            modifiers='inline'
            onClick={popupMenuState.dismiss}
            userId={props.user.id}
            wrapperClass='simple-menu__item'
          />
          <ReportReportable
            className='simple-menu__item'
            icon
            onFormOpen={popupMenuState.dismiss}
            reportableId={props.user.id.toString()}
            reportableType='user'
            user={props.user}
          />
        </div>
      </PopupMenu>
    </div>
  );
}
