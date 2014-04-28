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
        $shipment->getState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_READY);
        $factory->get($shipment, ShipmentTransitions::GRAPH)->shouldBeCalled()->willReturn($sm);
        $sm->apply('transitionName')->shouldBeCalled();

        $shipment->getItems()->shouldBeCalled()->willReturn(array($item));

        $item->getShippingState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_READY);
        $factory->get($item, ShipmentItemTransitions::GRAPH)->shouldBeCalled()->willReturn($sm);
        $sm->apply('transitionName')->shouldBeCalled();

        $this->updateShipmentStates(array($shipment), 'transitionName', ShipmentInterface::STATE_READY);
    }

    function it_does_not_update_shipment_states_if_state_from_does_not_match(
        $factory,
        ShipmentInterface $shipment,
        ShipmentItemInterface $item
    )
    {
        $shipment->getState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $factory->get(Argument::any())->shouldNotBeCalled();

        $item->getShippingState()->shouldNotBeCalled();
        $item->setShippingState(ShipmentInterface::STATE_SHIPPED)->shouldNotBeCalled();

        $this->updateShipmentStates(array($shipment), 'transitionName', ShipmentInterface::STATE_READY);
    }

    function it_updates_item_states($factory, ShipmentItemInterface $item, StateMachineInterface $sm)
    {
        $item->getShippingState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_READY);

        $factory->get($item, ShipmentItemTransitions::GRAPH)->shouldBeCalled()->willReturn($sm);
        $sm->apply('transitionName')->shouldBeCalled();

        $this->updateItemStates(array($item), 'transitionName', ShipmentInterface::STATE_READY);
    }

    function it_does_not_update_item_states_if_state_from_does_not_match($factory, ShipmentItemInterface $item)
    {
        $item->getShippingState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $factory->get(Argument::any())->shouldNotBeCalled();

        $this->updateItemStates(array($item), 'transitionName', ShipmentInterface::STATE_READY);
    }
}
