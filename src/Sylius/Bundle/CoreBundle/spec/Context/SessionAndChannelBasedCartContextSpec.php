<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Context\SessionAndChannelBasedCartContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class SessionAndChannelBasedCartContextSpec extends ObjectBehavior
{
    function let(SessionInterface $session, OrderRepositoryInterface $orderRepository, ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($session, 'session_key_name', $channelContext, $orderRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SessionAndChannelBasedCartContext::class);
    }

    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_returns_cart_based_on_id_stored_in_session_and_current_channel(
        SessionInterface $session,
        OrderRepositoryInterface $orderRepository,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        OrderInterface $cart
    ) {

        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('Poland');
        $session->has('session_key_name.Poland')->willReturn(true);
        $session->get('session_key_name.Poland')->willReturn(12345);

        $orderRepository->findCartByChannel(12345, $channel)->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_cart_not_found_exception_if_session_key_does_not_exist(
        SessionInterface $session,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('Poland');
        $session->has('session_key_name.Poland')->willReturn(false);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_throws_cart_not_found_exception_and_removes_id_from_session_when_cart_was_not_found(
        SessionInterface $session,
        OrderRepositoryInterface $orderRepository,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('Poland');
        $session->has('session_key_name.Poland')->willReturn(true);
        $session->get('session_key_name.Poland')->willReturn(12345);

        $orderRepository->findCartByChannel(12345, $channel)->willReturn(null);

        $session->remove('session_key_name.Poland')->shouldBeCalled();

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_throws_cart_not_found_exception_if_channel_was_not_found(ChannelContextInterface $channelContext)
    {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }
}
