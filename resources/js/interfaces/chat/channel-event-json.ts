// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { SocketEventData } from 'socket-message-event';
import { arrayHas } from 'utils/contains';
import ChannelJson from './channel-json';

const channelEvents = ['chat.channel.join', 'chat.channel.part'] as const;
type ChannelEvent = (typeof channelEvents)[number];

export function isChannelEvent(arg: SocketEventData): arg is ChannelEventJson {
  return arg.event != null && arrayHas(channelEvents, arg.event);
}

export default interface ChannelEventJson {
  data: ChannelJson;
  event: ChannelEvent;
}
