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

namespace spec\Sylius\Bundle\CoreBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartSessionStorageSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($requestStack, 'session_key_name', $orderRepository);
    }

    function it_implements_a_cart_storage_interface(): void
    {
        $this->shouldImplement(CartStorageInterface::class);
    }

    function it_returns_false_when_session_is_not_found_during_checking_if_cart_is_in_session(
        RequestStack $requestStack,
        ChannelInterface $channel,
    ): void {
        $channel->getCode()->willReturn('channel_code');

        $requestStack->getSession()->willThrow(SessionNotFoundException::class);

        $this->hasForChannel($channel)->shouldReturn(false);
    }

    function it_checks_if_cart_is_in_session(
        SessionInterface $session,
        RequestStack $requestStack,
        ChannelInterface $channel,
    ): void {
        $channel->getCode()->willReturn('channel_code');

        $requestStack->getSession()->willReturn($session);
        $session->has('session_key_name.channel_code')->willReturn(true);

        $this->hasForChannel($channel)->shouldReturn(true);
    }

    function it_sets_a_cart_in_a_session(
        SessionInterface $session,
        RequestStack $requestStack,
        ChannelInterface $channel,
        OrderInterface $cart,
    ): void {
        $channel->getCode()->willReturn('channel_code');
        $cart->getId()->willReturn(14);

        $requestStack->getSession()->willReturn($session);

        $session->set('session_key_name.channel_code', 14)->shouldBeCalled();

        $this->setForChannel($channel, $cart);
    }

    function it_returns_a_cart_from_a_session(
        SessionInterface $session,
        RequestStack $requestStack,
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
        OrderInterface $cart,
    ): void {
        $channel->getCode()->willReturn('channel_code');

        $requestStack->getSession()->willReturn($session);
        $session->has('session_key_name.channel_code')->willReturn(true);
        $session->get('session_key_name.channel_code')->willReturn(14);

        $orderRepository->findCartByChannel(14, $channel)->willReturn($cart);

        $this->getForChannel($channel)->shouldReturn($cart);
    }

    function it_removes_a_cart_from_a_session(SessionInterface $session, RequestStack $requestStack, ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn('channel_code');

        $requestStack->getSession()->willReturn($session);
        $session->remove('session_key_name.channel_code')->willReturn(null)->shouldBeCalled();

        $this->removeForChannel($channel);
    }
}
