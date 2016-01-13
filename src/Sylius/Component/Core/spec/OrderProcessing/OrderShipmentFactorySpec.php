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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin \Sylius\Component\Core\OrderProcessing\OrderShipmentFactory
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShipmentFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $shipmentFactory)
    {
        $this->beConstructedWith($shipmentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\OrderShipmentFactory');
    }

    function it_implements_Sylius_shipment_factory_interface()
    {
        $this->shouldImplement(OrderShipmentFactoryInterface::class);
    }

    function it_creates_a_single_shipment_and_assigns_all_item_units_to_it(
        FactoryInterface $shipmentFactory,
        OrderInterface $order,
        ShipmentInterface $shipment,
        OrderItemUnitInterface $itemUnit
    ) {

        $shipmentFactory
            ->createNew()
            ->willReturn($shipment)
        ;

        $order
            ->hasShipments()
            ->willReturn(false)
            ->shouldBeCalled()
        ;

        $order
            ->getItemUnits()
            ->willReturn(array($itemUnit))
        ;

        $shipment
            ->addItem($itemUnit)
            ->shouldBeCalled()
        ;

        $order
            ->addShipment($shipment)
            ->shouldBeCalled()
        ;

        $this->createForOrder($order);
    }

    function it_adds_new_item_units_to_existing_shipment(
        OrderInterface $order,
        ShipmentInterface $shipment,
        ArrayCollection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment
    ) {
        $shipments
            ->first()
            ->willReturn($shipment)
            ->shouldBeCalled()
        ;

        $order
            ->hasShipments()
            ->willReturn(true)
            ->shouldBeCalled()
        ;

        $itemUnit
            ->getShipment()
            ->willReturn($shipment)
        ;

        $order
            ->getItemUnits()
            ->willReturn(array(
                $itemUnit,
                $itemUnitWithoutShipment
            ))
        ;

        $order
            ->getShipments()
            ->willReturn($shipments)
            ->shouldBeCalled()
        ;

        $shipment
            ->addItem($itemUnitWithoutShipment)
            ->shouldBeCalled()
        ;

        $shipment
            ->addItem($itemUnit)
            ->shouldNotBeCalled()
        ;

        $this->createForOrder($order);
    }
}
