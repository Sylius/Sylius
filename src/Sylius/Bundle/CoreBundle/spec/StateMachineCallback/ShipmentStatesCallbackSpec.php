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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;

/**
 * @author Liverbool <nukboon@gmail.com>
 */
class ShipmentStatesCallbackSpec extends ObjectBehavior
{
    function let(ShipmentProcessorInterface $processor)
    {
        $this->beConstructedWith($processor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\ShipmentStatesCallback');
    }

    function it_updates_order_shipment_states_to_backordered(
        ShipmentInterface $shipment,
        OrderInterface $order
    ) {
        $order->isBackorder()->willReturn(true);
        $order->getShipments()->willReturn([$shipment]);

        $this->updateOrderShipmentStates($order, 'backorder');
    }

    function it_updates_order_shipment_states_to_other_if_order_is_not_be_backordered(
        ShipmentInterface $shipment,
        OrderInterface $order
    ) {
        $order->isBackorder()->willReturn(false);
        $order->getShipments()->willReturn([$shipment]);

        $this->updateOrderShipmentStates($order, 'foo');
    }
}
