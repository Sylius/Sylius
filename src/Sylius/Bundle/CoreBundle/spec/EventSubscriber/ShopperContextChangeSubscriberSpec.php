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
use Sylius\Bundle\CoreBundle\SyliusCoreEvents;
use Sylius\Component\Cart\Provider\CartProviderInterface;
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

    function it_dispatches_all_core_cart_events(
        EventDispatcherInterface $eventDispatcher,
        CartProviderInterface $cartProvider
    ) {
        $cartProvider->getCart()->shouldBeCalled();

        $eventDispatcher->dispatch(
            SyliusCoreEvents::PRE_CART_CHANGE,
            Argument::type(GenericEvent::class)
        )->shouldBeCalled(10);
        $eventDispatcher->dispatch(SyliusCoreEvents::CART_CHANGE)->shouldBeCalled();
        $eventDispatcher->dispatch(SyliusCoreEvents::POST_CART_CHANGE)->shouldBeCalled();

        $this->onShopperContextChange();
    }
}