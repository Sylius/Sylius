<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CartBundle\Provider\SessionCartProvider;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @mixin SessionCartProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SessionCartProviderSpec extends ObjectBehavior
{
    function let(SessionInterface $session, CartRepositoryInterface $cartRepository)
    {
        $this->beConstructedWith($session, 'session_key_name', $cartRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Provider\SessionCartProvider');
    }

    function it_implements_cart_provider_interface()
    {
        $this->shouldImplement(CartProviderInterface::class);
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

    function it_returns_null_when_there_is_not_id_stored_in_session(SessionInterface $session)
    {
        $session->has('session_key_name')->willReturn(false);
        
        $this->getCart()->shouldReturn(null);
    }

    function it_returns_null_and_removes_id_from_session_when_cart_is_not_found(
        SessionInterface $session,
        CartRepositoryInterface $cartRepository
    ) {
        $session->has('session_key_name')->willReturn(true);
        $session->get('session_key_name')->willReturn(12345);
        $cartRepository->findCartById(12345)->willReturn(null);

        $session->remove('session_key_name')->shouldBeCalled();

        $this->getCart()->shouldReturn(null);
    }
}
