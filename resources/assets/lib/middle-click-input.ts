// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

export default class MiddleClickInput {
  constructor() {
    $(document).on('mousedown', 'textarea, input', this.activateOnClick);
  }

  activateOnClick(e: JQuery.MouseDownEvent) {
    // this is special case for middle click; proceed as normal for everything else
    if (e.which !== 2) return;

    const elem = e.currentTarget;

    // do default browser thing if the box is already focused
    if (elem === document.activeElement) return;

    // prevent showing autoscroll
    e.preventDefault();
    e.stopPropagation();
    // manually trigger focus otherwise nothing will happen because of preventDefault
    elem.focus();
  }
}
