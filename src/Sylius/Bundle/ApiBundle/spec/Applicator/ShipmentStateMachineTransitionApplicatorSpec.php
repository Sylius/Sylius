<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Applicator;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachine;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentTransitions;

final class ShipmentStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_ships_shipment(
        StateMachineFactoryInterface $stateMachineFactory,
        ShipmentInterface $shipment,
        StateMachine $stateMachine
    ): void {
        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $this->ship($shipment);
    }
}
