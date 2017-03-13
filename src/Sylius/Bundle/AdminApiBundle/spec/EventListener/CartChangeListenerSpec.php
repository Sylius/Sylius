<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminApiBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminApiBundle\EventListener\CartChangeListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartChangeListenerSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor, ObjectManager $manager)
    {
        $this->beConstructedWith($orderProcessor, $manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartChangeListener::class);
    }

    function it_recalculates_cart_on_add(OrderProcessorInterface $orderProcessor, ObjectManager $manager, GenericEvent $event, OrderItemInterface $orderItem, OrderInterface $order)
    {
        $event->getSubject()->willReturn($orderItem);
        $orderItem->getOrder()->willReturn($order);

        $orderProcessor->process($order)->shouldBeCalled();
        $manager->persist($order)->shouldBeCalled();

        $this->recalculateOrderOnAdd($event);
    }

    function it_recalculates_cart_and_remove_item_on_delete(OrderProcessorInterface $orderProcessor, ObjectManager $manager, GenericEvent $event, OrderItemInterface $orderItem, OrderInterface $order)
    {
        $event->getSubject()->willReturn($orderItem);
        $orderItem->getOrder()->willReturn($order);

        $order->removeItem($orderItem)->shouldBeCalled();
        $orderProcessor->process($order)->shouldBeCalled();

        $this->recalculateOrderOnDelete($event);
    }
}
