<?php

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CartBundle\EventListener\RefreshCartListener;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

class RefreshCartListenerSpec extends ObjectBehavior
{
    function let(CartProviderInterface $cartProvider)
    {
        $this->beConstructedWith($cartProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RefreshCartListener::class);
    }

    function it_refresh_the_cart(
        Event $event,
        CartInterface $cart,
        CartProviderInterface $cartProvider
    )
    {
        $cartProvider->getCart()->shouldBeCalled()->willReturn($cart);
        $cart->calculateTotal()->shouldBeCalled();

        $this->refreshCart($event);
    }

    function it_throw_exception_if_subject_is_not_a_cart(
        Event $event,
        CartInterface $cart,
        CartProviderInterface $cartProvider
    )
    {
        $cartProvider->getCart()->shouldBeCalled()->willReturn(null);
        $cart->calculateTotal()->shouldNotBeCalled();

        $this->shouldThrow('\InvalidArgumentException')->during('refreshCart', array($event));
    }
}
