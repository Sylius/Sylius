<?php

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Model\CartInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class RefreshCartListenerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\RefreshCartListener');
    }

    public function it_refresh_the_cart(GenericEvent $event, CartInterface $cart)
    {
        $event->getSubject()->shouldBeCalled()->willReturn($cart);
        $cart->calculateTotal()->shouldBeCalled();

        $this->refreshCart($event);
    }

    public function it_throw_exception_if_subject_is_not_a_cart(GenericEvent $event, CartInterface $cart)
    {
        $event->getSubject()->shouldBeCalled()->willReturn(null);
        $cart->calculateTotal()->shouldNotBeCalled();

        $this->shouldThrow('\InvalidArgumentException')->during('refreshCart', array($event));
    }
}
