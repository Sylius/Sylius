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
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\StateMachineCallback\OrderItemUnitCallback;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentTransitions;

/**
 * @author Robin Jansen <robinjansen51@gmail.com>
 */
class OrderItemUnitCallbackSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItemUnitCallback::class);
    }

    function it_updates_shipment_state_when_no_unit_in_backorder(
        $factory,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        ShipmentInterface $shipment,
        StateMachineInterface $stateMachine
    ) {
        $unit1->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn([$unit2]);

        $unit2->getInventoryState()
            ->shouldBeCalled()
            ->willReturn(OrderItemUnitInterface::STATE_ONHOLD)
        ;

        $factory->get($shipment, ShipmentTransitions::GRAPH)
            ->shouldBeCalled()
            ->willReturn($stateMachine)
        ;

        $stateMachine->apply(ShipmentTransitions::SYLIUS_PREPARE)->shouldBeCalled();

        $this->updateShipmentStateOnInventoryRestock($unit1);
    }

    function it_does_not_update_shipment_state_when_units_in_backorder(
        $factory,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        OrderItemUnitInterface $unit3,
        ShipmentInterface $shipment
    ) {
        $unit1->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn([$unit2, $unit3]);

        $unit2->getInventoryState()
            ->shouldBeCalled()
            ->willReturn(OrderItemUnitInterface::STATE_ONHOLD)
        ;

        $unit3->getInventoryState()
            ->shouldBeCalled()
            ->willReturn(OrderItemUnitInterface::STATE_BACKORDERED)
        ;

        $factory->get($shipment, ShipmentTransitions::GRAPH)
            ->shouldNotBeCalled()
        ;

        $this->updateShipmentStateOnInventoryRestock($unit1);
    }
}
