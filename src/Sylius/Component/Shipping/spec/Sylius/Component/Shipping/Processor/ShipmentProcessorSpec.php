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

use SM\Factory\FactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentItemInterface;
use Sylius\Component\Shipping\ShipmentItemTransitions;
use Sylius\Component\Shipping\ShipmentTransitions;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShipmentProcessorSpec extends ObjectBehavior
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
        $this->shouldImplement('Sylius\Component\Shipping\Processor\ShipmentProcessorInterface');
    }

    function it_updates_shipment_states(
        $factory,
        ShipmentInterface $shipment,
        StateMachineInterface $sm
    ) {
        $factory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($sm);

        $sm->apply('transition', true)->shouldBeCalled();

        $this->updateShipmentStates(array($shipment), 'transition');
    }

    function it_updates_item_states(
        $factory,
        ShipmentItemInterface $item,
        StateMachineInterface $sm
    ) {
        $factory->get($item, ShipmentItemTransitions::GRAPH)->shouldBeCalled()->willReturn($sm);

        $sm->apply('transition', true)->shouldBeCalled();

        $this->updateItemStates(array($item), 'transition');
    }
}
