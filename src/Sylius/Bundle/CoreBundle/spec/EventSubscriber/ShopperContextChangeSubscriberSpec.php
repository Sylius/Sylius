<?php
/**
 * Created by PhpStorm.
 * User: piotrwalkow
 * Date: 16/11/2015
 * Time: 11:56
 */

namespace spec\Sylius\Bundle\CoreBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\EventSubscriber\ShopperContextChangeSubscriber;
use Symfony\Component\EventDispatcher\GenericEvent;

class ShopperContextChangeSubscriberSpec extends ObjectBehavior
{
    function let(
        EventDispatcherInterface $eventDispatcher,
        CartProviderInterface $cartProvider
    ) {
        $this->beConstructedWith($eventDispatcher, $cartProvider);
    }

    function it_is_initializable() {
        $this->shouldHaveType(ShopperContextChangeSubscriber::class);
    }

    function it_dispatches_cart_initialize(
        GenericEvent $event,
        CartProviderInterface $cartProvider,
        EventDispatcherInterface $eventDispatcher,
        CartInterface $cart
    ) {
        $cartProvider->getCart()->willReturn($cart);

        $eventDispatcher->dispatch(
            SyliusCartEvents::CART_INITIALIZE,
            Argument::type(CartEvent::class)
        )->shouldBeCalled();

        $this->onPreCartChange($event);
    }

    function it_dispatches_cart_change(
        GenericEvent $event,
        CartProviderInterface $cartProvider,
        EventDispatcherInterface $eventDispatcher,
        CartInterface $cart
    ) {
        $cartProvider->getCart()->willReturn($cart);

        $eventDispatcher->dispatch(
            SyliusCartEvents::CART_CHANGE,
            Argument::type(GenericEvent::class)
        )->shouldBeCalled();

        $this->onCartChange($event);
    }

    function it_dispatches_cart_save_initialize(
        GenericEvent $event,
        CartProviderInterface $cartProvider,
        EventDispatcherInterface $eventDispatcher,
        CartInterface $cart
    ) {
        $cartProvider->getCart()->willReturn($cart);

        $eventDispatcher->dispatch(
            SyliusCartEvents::CART_SAVE_INITIALIZE,
            Argument::type(CartEvent::class)
        )->shouldBeCalled();

        $this->onPostCartChange($event);
    }
}