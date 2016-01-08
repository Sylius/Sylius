<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\StateMachineCallback;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Sylius\Component\Shipping\ShipmentTransitions;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderShipmentCallbackSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\OrderShipmentCallback');
    }

    function it_updates_order_state(
        $factory,
        ShipmentInterface $shipment,
        OrderInterface $order,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getShipments()->willReturn([$shipment]);

        $factory->get($order, OrderTransitions::GRAPH)->willReturn($sm1);
        $factory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($sm2);

        $sm2->can(ShipmentTransitions::SYLIUS_SHIP)->shouldBeCalled()->willReturn(false);

        $sm1->apply(OrderTransitions::SYLIUS_SHIP, true)->shouldBeCalled();

        $this->updateOrderShippingState($order);
    }

    function it_does_not_update_order_state_if_one_shipment_is_not_shipped(
        $factory,
        ShipmentInterface $shipment,
        OrderInterface $order,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getShipments()->willReturn([$shipment]);

        $factory->get($order, OrderTransitions::GRAPH)->willReturn($sm1);
        $factory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($sm2);

        $sm2->can(ShipmentTransitions::SYLIUS_SHIP)->shouldBeCalled()->willReturn(false);

        $sm1->apply(OrderTransitions::SYLIUS_SHIP, true)->shouldBeCalled();

        $this->updateOrderShippingState($order);
    }
}
