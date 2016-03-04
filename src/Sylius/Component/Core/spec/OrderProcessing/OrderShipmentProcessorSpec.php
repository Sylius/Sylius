<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin \Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShipmentProcessorSpec extends ObjectBehavior
{
    function let(FactoryInterface $shipmentFactory)
    {
        $this->beConstructedWith($shipmentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor');
    }

    function it_implements_Sylius_shipment_factory_interface()
    {
        $this->shouldImplement(OrderShipmentProcessorInterface::class);
    }

    function it_creates_a_single_shipment_and_assigns_all_units_to_it(
        FactoryInterface $shipmentFactory,
        OrderInterface $order,
        ShipmentInterface $shipment,
        OrderItemUnitInterface $itemUnit1,
        OrderItemUnitInterface $itemUnit2
    ) {
        $shipmentFactory->createNew()->willReturn($shipment);

        $order->hasShipments()->willReturn(false);
        $order->getItemUnits()->willReturn([$itemUnit1, $itemUnit2]);

        $shipment->addUnit($itemUnit1)->shouldBeCalled();
        $shipment->addUnit($itemUnit2)->shouldBeCalled();

        $order->addShipment($shipment)->shouldBeCalled();

        $this->processOrderShipment($order);
    }

    function it_adds_new_item_units_to_existing_shipment(
        OrderInterface $order,
        ShipmentInterface $shipment,
        Collection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment
    ) {
        $shipments->first()->willReturn($shipment);

        $order->hasShipments()->willReturn(true);
        $order->getItemUnits()->willReturn([$itemUnit, $itemUnitWithoutShipment]);
        $order->getShipments()->willReturn($shipments);

        $itemUnit->getShipment()->willReturn($shipment);

        $shipment->addUnit($itemUnitWithoutShipment)->shouldBeCalled();
        $shipment->addUnit($itemUnit)->shouldNotBeCalled();

        $this->processOrderShipment($order);
    }
}
