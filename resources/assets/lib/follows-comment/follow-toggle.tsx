// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import FollowCommentJson from 'interfaces/follow-comment-json';
import { route } from 'laroute';
import * as React from 'react';
import { Spinner } from 'spinner';
import { classWithModifiers } from 'utils/css';

interface Props {
  follow: FollowCommentJson;
  following: boolean;
}

interface State {
  following: boolean;
  toggling: boolean;
}

export default class FollowToggle extends React.PureComponent<Props, State> {
  static defaultProps = {
    following: true,
  };

  state: State;

  private toggleXhr: null | JQueryXHR = null;

  constructor(props: Props) {
    super(props);

    this.state = {
      following: this.props.following,
      toggling: false,
    };
  }

  render() {
    return (
      <button
        type='button'
        className={classWithModifiers('btn-circle', { activated: this.state.following })}
        onClick={this.onClick}
        disabled={this.state.toggling}
      >
        {
          this.state.toggling
            ? <Spinner />
            : <span className='fas fa-bell' />
        }
      </button>
    );
  }

  private onClick = () => {
    const params = {
      follow: {
        notifiable_id: this.props.follow.notifiable_id,
        notifiable_type: this.props.follow.notifiable_type,
        subtype: this.props.follow.subtype,
      },
    };

    const method = this.state.following ? 'DELETE' : 'POST';

    this.toggleXhr?.abort();

    this.setState({ toggling: true }, () => {
      this.toggleXhr = $.ajax(route('follows.store'), { method, data: params })
        .done(() => {
          this.setState({ following: !this.state.following });
        }).always(() => {
          this.setState({ toggling: false });
        });
    });
  }
}
