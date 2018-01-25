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

namespace spec\Sylius\Bundle\OrderBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionBasedCartContextSpec extends ObjectBehavior
{
    function let(SessionInterface $session, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($session, 'session_key_name', $orderRepository);
    }

    function it_implements_a_cart_context_interface(): void
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_returns_a_cart_based_on_id_stored_in_session(
        SessionInterface $session,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart
    ): void {
        $session->has('session_key_name')->willReturn(true);
        $session->get('session_key_name')->willReturn(12345);
        $orderRepository->findCartById(12345)->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_a_cart_not_found_exception_if_session_key_does_not_exist(SessionInterface $session): void
    {
        $session->has('session_key_name')->willReturn(false);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_throws_a_cart_not_found_exception_and_removes_id_from_session_when_cart_is_not_found(
        SessionInterface $session,
        OrderRepositoryInterface $orderRepository
    ): void {
        $session->has('session_key_name')->willReturn(true);
        $session->get('session_key_name')->willReturn(12345);
        $orderRepository->findCartById(12345)->willReturn(null);

        $session->remove('session_key_name')->shouldBeCalled();

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }
}
