// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { action, autorun, makeObservable, observable } from 'mobx';
import { disposeOnUnmount, observer } from 'mobx-react';
import * as React from 'react';
import { blackoutToggle } from 'utils/blackout';
import { classWithModifiers, Modifiers } from 'utils/css';

const bn = 'select-options';

export interface Option {
  id: string | number | null;
  text: string;
}

interface ComponentOptionRenderProps<T extends Option> {
  onClick: (event: React.MouseEvent) => void;
  option: T;
  selected?: boolean;
  withChevron: boolean;
}

interface Props<T extends Option> {
  blackout: boolean;
  href?: (option: T) => string;
  modifiers?: Modifiers;
  onChange: (option: T) => void;
  options: T[];
  renderOption?(option: T): React.ReactNode;
  selected: T;
}

@observer
export default class SelectOptions<T extends Option> extends React.Component<Props<T>> {
  static readonly defaultProps = { blackout: true };

  private readonly ref = React.createRef<HTMLDivElement>();
  @observable private showingSelector = false;

  constructor(props: Props<T>) {
    super(props);
    makeObservable(this);
    disposeOnUnmount(this, autorun(() => {
      blackoutToggle(this, this.props.blackout && this.showingSelector);
    }));
  }

  componentDidMount() {
    document.addEventListener('click', this.hideSelector);
  }

  componentWillUnmount() {
    document.removeEventListener('click', this.hideSelector);
    blackoutToggle(this, false);
  }

  render() {
    const className = classWithModifiers(
      bn,
      { selecting: this.showingSelector },
      this.props.modifiers,
    );

    return (
      <div ref={this.ref} className={className}>
        <div className={`${bn}__select`}>
          {this.renderOption({
            onClick: this.toggleSelector,
            option: this.props.selected,
            withChevron: true,
          })}
        </div>

        <div className={`${bn}__selector`}>
          {this.renderOptions()}
        </div>
      </div>
    );
  }

  // dismiss the selector if clicking anywhere outside of it.
  @action
  private readonly hideSelector = (e: MouseEvent) => {
    if (e.button === 0 && this.ref.current != null && !e.composedPath().includes(this.ref.current)) {
      this.showingSelector = false;
    }
  };

  @action
  private readonly optionSelected = (event: React.MouseEvent, option: T) => {
    if (event.button !== 0) return;

    event.preventDefault();
    this.showingSelector = false;
    this.props.onChange?.(option);
  };

  private renderOption({ onClick, option, selected = false, withChevron }: ComponentOptionRenderProps<T>) {
    const cssClasses = classWithModifiers(`${bn}__option`, { selected });

    const text = this.props.renderOption == null
      ? <div className='u-ellipsis-overflow'>{option.text}</div>
      : this.props.renderOption(option);

    return (
      <a
        key={option.id}
        className={cssClasses}
        href={this.props.href?.(option)}
        onClick={onClick}
      >
        {text}
        {withChevron && (
          <div className={`${bn}__decoration`}>
            <span className='fas fa-chevron-down' />
          </div>
        )}
      </a>
    );
  }

  private renderOptions() {
    return this.props.options.map((option) => this.renderOption({
      onClick: (event: React.MouseEvent) => {
        this.optionSelected(event, option);
      },
      option,
      selected: this.props.selected?.id === option.id,
      withChevron: false,
    }));
  }

  @action
  private readonly toggleSelector = (event: React.MouseEvent) => {
    if (event.button !== 0) return;

    event.preventDefault();
    this.showingSelector = !this.showingSelector;
  };
}
