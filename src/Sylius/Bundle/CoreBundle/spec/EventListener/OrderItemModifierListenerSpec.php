<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderItemModifierListenerSpec extends ObjectBehavior
{
    function let(OrderModifierInterface $orderModifier): void
    {
        $this->beConstructedWith($orderModifier);
    }

    function it_uses_order_processor_to_recalculate_order(
        OrderModifierInterface $orderModifier,
        GenericEvent $event,
        OrderItemInterface $orderItem,
        OrderInterface $order,
    ): void {
        $orderItem->getOrder()->willReturn($order);
        $event->getSubject()->willReturn($orderItem);

        $this->removeOrderItem($event);

        $orderModifier->removeFromOrder($order, $orderItem)->shouldHaveBeenCalled();
    }

    function it_throws_exception_if_event_subject_is_not_order_item(GenericEvent $event): void
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('removeOrderItem', [$event])
        ;
    }
}
