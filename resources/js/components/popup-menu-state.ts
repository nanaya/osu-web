// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { observable, makeObservable, action } from 'mobx';
import * as React from 'react';

export default class PopupMenuState {
  @observable active = false;
  buttonRefCurrent: HTMLElement | null = null;
  menuRef = React.createRef<HTMLDivElement>();
  menuRootRef = React.createRef<HTMLDivElement>();

  constructor() {
    makeObservable(this);
  }

  @action
  readonly dismiss = () => {
    this.active = false;
  };

  readonly setButtonRef = (button: HTMLElement | null) => {
    this.buttonRefCurrent = button;
  };

  @action
  readonly toggle = () => {
    this.active = !this.active;
  };
}
