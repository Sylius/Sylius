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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Sylius\Bundle\CartBundle\Storage\SessionCartStorage;
use Sylius\Component\Cart\Model\CartInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SessionCartStorageSpec extends ObjectBehavior
{
    public function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Storage\SessionCartStorage');
    }

    public function it_implements_Sylius_cart_storage_interface()
    {
        $this->shouldImplement('Sylius\Component\Cart\Storage\CartStorageInterface');
    }

    public function it_returns_cart_identifier_via_session($session)
    {
        $session->get(SessionCartStorage::KEY)->willReturn(7);

        $this->getCurrentCartIdentifier()->shouldReturn(7);
    }

    public function it_sets_cart_identifier_via_session($session, CartInterface $cart)
    {
        $cart->getIdentifier()->will(function () {
            return 3;
        });
        $session->set(SessionCartStorage::KEY, 3)->will(function () use ($session) {
            $session->get(SessionCartStorage::KEY)->willReturn(3);
        });

        $this->setCurrentCartIdentifier($cart);

        $this->getCurrentCartIdentifier()->shouldReturn(3);
    }

    public function it_removes_the_saved_identifier_from_session_on_reset($session)
    {
        $session->remove(SessionCartStorage::KEY)->shouldBeCalled();

        $this->resetCurrentCartIdentifier();
    }
}
