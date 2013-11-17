<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Storage\SessionCartStorage as SessionCartStorageClass;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SessionCartStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Storage\SessionCartStorage');
    }

    function it_implements_Sylius_cart_storage_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Storage\CartStorageInterface');
    }

    function it_returns_cart_identifier_via_session($session)
    {
        $session->get(SessionCartStorageClass::KEY)->willReturn(7);

        $this->getCurrentCartIdentifier()->shouldReturn(7);
    }

    function it_sets_cart_identifier_via_session($session, CartInterface $cart)
    {
        $cart->getIdentifier()->willReturn(3);
        $session->set(SessionCartStorageClass::KEY, 3)->shouldBeCalled();

        $this->setCurrentCartIdentifier($cart);
    }

    function it_removes_the_saved_identifier_from_session_on_reset($session)
    {
        $session->remove(SessionCartStorageClass::KEY)->shouldBeCalled();

        $this->resetCurrentCartIdentifier();
    }
}
