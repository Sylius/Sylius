<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Channel\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class ChannelDeletionCheckerSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    function it_returns_a_channel_can_be_deleted_when_is_disabled(ChannelInterface $channel): void
    {
        $channel->isEnabled()->willReturn(false);

        $this->isDeletable($channel)->shouldReturn(true);
    }

    function it_returns_a_channel_can_be_deleted_when_at_least_two_channels_are_enabled(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ChannelInterface $anotherChannel,
    ): void {
        $channel->isEnabled()->willReturn(true);
        $channelRepository->findEnabled()->willReturn([$channel, $anotherChannel]);

        $this->isDeletable($channel)->shouldReturn(true);
    }

    function it_returns_a_channel_cannot_be_deleted_when_only_one_channel_is_enabled(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $channel->isEnabled()->willReturn(true);
        $channelRepository->findEnabled()->willReturn([$channel]);

        $this->isDeletable($channel)->shouldReturn(false);
    }

    function it_returns_a_channel_cannot_be_deleted_when_no_channel_is_enabled(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $channel->isEnabled()->willReturn(true);
        $channelRepository->findEnabled()->willReturn([]);

        $this->isDeletable($channel)->shouldReturn(false);
    }
}
