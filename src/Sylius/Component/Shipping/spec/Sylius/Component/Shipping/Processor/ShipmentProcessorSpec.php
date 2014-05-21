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

use Finite\Factory\FactoryInterface;
use Finite\StateMachine\StateMachineInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\StateMachine\StateMachine;
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
        ShipmentItemInterface $item,
        StateMachineInterface $sm
    )
    {
        $factory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($sm);

        $sm->can('transition')->willReturn(true);
        $sm->apply('transition')->shouldBeCalled();

        $this->updateShipmentStates(array($shipment), 'transition');
    }

    function it_does_not_update_shipment_states_if_transition_cannot(
        $factory,
        ShipmentInterface $shipment,
        StateMachineInterface $sm
    ) {
        $factory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($sm);

        $sm->can('transition')->willReturn(false);
        $sm->apply('transition')->shouldNotBeCalled();

        $this->updateShipmentStates(array($shipment), 'transition');
    }

    function it_updates_item_states($factory, ShipmentItemInterface $item, StateMachineInterface $sm)
    {
        $factory->get($item, ShipmentItemTransitions::GRAPH)->shouldBeCalled()->willReturn($sm);

        $sm->can('transition')->willReturn(true);
        $sm->apply('transition')->shouldBeCalled();

        $this->updateItemStates(array($item), 'transition');
    }

    function it_does_not_update_item_states_if_transition_cannot($factory, ShipmentItemInterface $item, StateMachine $sm)
    {
        $factory->get($item, ShipmentItemTransitions::GRAPH)->willReturn($sm);

        $sm->can('transition')->willReturn(false);
        $sm->apply('transition')->shouldNotBeCalled();

        $this->updateItemStates(array($item), 'transition');
    }
}
