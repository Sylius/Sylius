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

namespace spec\Sylius\Bundle\CoreBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartSessionStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($session, 'session_key_name', $orderRepository);
    }

    function it_implements_a_cart_storage_interface(): void
    {
        $this->shouldImplement(CartStorageInterface::class);
    }

    function it_sets_a_cart_in_a_session(SessionInterface $session, ChannelInterface $channel, OrderInterface $cart): void
    {
        $channel->getCode()->willReturn('channel_code');
        $cart->getId()->willReturn(14);

        $session->set('session_key_name.channel_code', 14)->shouldBeCalled();

        $this->setForChannel($channel, $cart);
    }

    function it_returns_a_cart_from_a_session(
        SessionInterface $session,
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
        OrderInterface $cart
    ): void {
        $channel->getCode()->willReturn('channel_code');

        $session->has('session_key_name.channel_code')->willReturn(true);
        $session->get('session_key_name.channel_code')->willReturn(14);

        $orderRepository->findCartByChannel(14, $channel)->willReturn($cart);

        $this->getForChannel($channel)->shouldReturn($cart);
    }

    function it_removes_a_cart_from_a_session(SessionInterface $session, ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn('channel_code');

        $session->remove('session_key_name.channel_code')->shouldBeCalled();

        $this->removeForChannel($channel);
    }
}
