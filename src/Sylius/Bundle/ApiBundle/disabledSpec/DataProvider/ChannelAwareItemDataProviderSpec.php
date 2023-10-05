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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelAwareItemDataProviderSpec extends ObjectBehavior
{
    function let(ItemDataProviderInterface $itemDataProvider, ChannelContextInterface $channelContext): void
    {
        $this->beConstructedWith($itemDataProvider, $channelContext);
    }

    function it_is_an_item_data_provider(): void
    {
        $this->shouldImplement(ItemDataProviderInterface::class);
    }

    function it_adds_channel_to_the_context_if_not_there_yet(
        ItemDataProviderInterface $itemDataProvider,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $itemDataProvider->getItem('Class', 'ID', 'get', [ContextKeys::CHANNEL => $channel])->willReturn(null);

        $this->getItem('Class', 'ID', 'get', [])->shouldReturn(null);
    }

    function it_does_not_add_channel_to_the_context_silently_if_it_could_not_be_found(
        ItemDataProviderInterface $itemDataProvider,
        ChannelContextInterface $channelContext,
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $itemDataProvider->getItem('Class', 'ID', 'get', [])->willReturn(null);

        $this->getItem('Class', 'ID', 'get', [])->shouldReturn(null);
    }

    function it_does_not_add_channel_to_the_context_if_it_is_already_added(
        ItemDataProviderInterface $itemDataProvider,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->shouldNotBeCalled();

        $itemDataProvider->getItem('Class', 'ID', 'get', [ContextKeys::CHANNEL => $channel])->willReturn(null);

        $this->getItem('Class', 'ID', 'get', [ContextKeys::CHANNEL => $channel])->shouldReturn(null);
    }
}
