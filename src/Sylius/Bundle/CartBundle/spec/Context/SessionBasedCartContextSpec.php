<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SessionBasedCartContextSpec extends ObjectBehavior
{
    function let(SessionInterface $session, CartRepositoryInterface $cartRepository)
    {
        $this->beConstructedWith($session, 'session_key_name', $cartRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Context\SessionBasedCartContext');
    }

    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_returns_cart_based_on_id_stored_in_session(
        SessionInterface $session,
        CartRepositoryInterface $cartRepository,
        CartInterface $cart
    )  {
        $session->has('session_key_name')->willReturn(true);
        $session->get('session_key_name')->willReturn(12345);
        $cartRepository->findCartById(12345)->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_cart_not_found_exception_if_session_key_does_not_exist(SessionInterface $session)
    {
        $session->has('session_key_name')->willReturn(false);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_throws_cart_not_found_exception_and_removes_id_from_session_when_cart_is_not_found(
        SessionInterface $session,
        CartRepositoryInterface $cartRepository
    ) {
        $session->has('session_key_name')->willReturn(true);
        $session->get('session_key_name')->willReturn(12345);
        $cartRepository->findCartById(12345)->willReturn(null);

        $session->remove('session_key_name')->shouldBeCalled();

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }
}
