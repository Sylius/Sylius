<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Channel\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class SingleChannelContextSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    function it_implements_channel_context_interface(): void
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_returns_a_channel_if_it_is_the_only_one_defined(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel
    ): void {
        $channelRepository->findAll()->willReturn([$channel]);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_throws_a_channel_not_found_exception_if_there_are_no_channels_defined(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->findAll()->willReturn([]);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_throws_a_channel_not_found_exception_if_there_are_many_channels_defined(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel
    ): void {
        $channelRepository->findAll()->willReturn([$firstChannel, $secondChannel]);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }
}
