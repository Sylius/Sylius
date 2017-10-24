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

namespace spec\Sylius\Bundle\CoreBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;

final class SessionAndChannelBasedCartContextSpec extends ObjectBehavior
{
    function let(CartStorageInterface $cartStorage, ChannelContextInterface $channelContext): void
    {
        $this->beConstructedWith($cartStorage, $channelContext);
    }

    function it_implements_cart_context_interface(): void
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_returns_cart_based_on_id_stored_in_session_and_current_channel(
        CartStorageInterface $cartStorage,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        OrderInterface $cart
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $cartStorage->hasForChannel($channel)->willReturn(true);
        $cartStorage->getForChannel($channel)->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_cart_not_found_exception_if_session_key_does_not_exist(
        CartStorageInterface $cartStorage,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $cartStorage->hasForChannel($channel)->willReturn(false);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_throws_cart_not_found_exception_and_removes_id_from_session_when_cart_was_not_found(
        CartStorageInterface $cartStorage,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $cartStorage->hasForChannel($channel)->willReturn(true);
        $cartStorage->getForChannel($channel)->willReturn(null);

        $cartStorage->removeForChannel($channel)->shouldBeCalled();

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_throws_cart_not_found_exception_if_channel_was_not_found(ChannelContextInterface $channelContext): void
    {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }
}
