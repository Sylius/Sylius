<?php

namespace spec\Sylius\Bundle\CartBundle\Storage;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\CartBundle\Storage\SessionCartStorage as SessionCartStorageClass;

/**
 * Session cart storage spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SessionCartStorage extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    function let($session)
    {
        $this->beConstructedWith($session);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Storage\SessionCartStorage');
    }

    function it_should_be_Sylius_cart_storage()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Storage\CartStorageInterface');
    }

    function it_should_retrieve_cart_id_via_session($session)
    {
        $session->get(SessionCartStorageClass::KEY)->willReturn(7);

        $this->getCurrentCartIdentifier()->shouldReturn(7);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_set_cart_id_via_session($session, $cart)
    {
        $cart->getId()->willReturn(3);
        $session->set(SessionCartStorageClass::KEY, 3)->shouldBeCalled();

        $this->setCurrentCartIdentifier($cart);
    }

    function it_should_remove_the_saved_id_from_session_on_reset($session)
    {
        $session->remove(SessionCartStorageClass::KEY)->shouldBeCalled();

        $this->resetCurrentCartIdentifier();
    }
}
