<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Processor;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Sylius\Component\Shipping\ShipmentUnitTransitions;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class ShipmentProcessorSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Processor\ShipmentProcessor');
    }

    function it_implements_Sylius_shipment_processor_interface()
    {
        $this->shouldImplement(ShipmentProcessorInterface::class);
    }

    function it_updates_shipment_states(
        $factory,
        ShipmentInterface $shipment,
        StateMachineInterface $sm
    ) {
        $factory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($sm);

        $sm->apply('transition', true)->shouldBeCalled();

        $this->updateShipmentStates([$shipment], 'transition');
    }

    function it_updates_unit_states(
        $factory,
        ShipmentUnitInterface $unit,
        StateMachineInterface $sm
    ) {
        $factory->get($unit, ShipmentUnitTransitions::GRAPH)->shouldBeCalled()->willReturn($sm);

        $sm->apply('transition', true)->shouldBeCalled();

        $this->updateUnitStates([$unit], 'transition');
    }
}
