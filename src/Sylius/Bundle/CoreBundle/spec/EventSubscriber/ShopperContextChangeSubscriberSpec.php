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
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($eventDispatcher);
    }

    function it_is_initializable() {
        $this->shouldHaveType(ShopperContextChangeSubscriber::class);
    }

    function it_dispatches_all_core_cart_events(
        EventDispatcherInterface $eventDispatcher
    ) {
        $eventDispatcher->dispatch(SyliusCoreEvents::PRE_CART_CHANGE)->shouldBeCalled(10);
        $eventDispatcher->dispatch(SyliusCoreEvents::CART_CHANGE)->shouldBeCalled();
        $eventDispatcher->dispatch(SyliusCoreEvents::POST_CART_CHANGE)->shouldBeCalled();

        $this->onShopperContextChange();
    }
}