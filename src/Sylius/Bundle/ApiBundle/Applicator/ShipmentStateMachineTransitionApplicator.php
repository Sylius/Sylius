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

namespace Sylius\Bundle\ApiBundle\Applicator;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/** @experimental */
final class ShipmentStateMachineTransitionApplicator implements ShipmentStateMachineTransitionApplicatorInterface
{
    private StateMachineFactoryInterface $stateMachineFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        StateMachineFactoryInterface $stateMachineFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function ship(ShipmentInterface $data): ShipmentInterface
    {
        $this->applyTransition($data, ShipmentTransitions::TRANSITION_SHIP);

        $this->eventDispatcher->dispatch(new GenericEvent($data), 'sylius.shipment.post_ship');

        return $data;
    }

    private function applyTransition(ShipmentInterface $shipment, string $transition): void
    {
        $stateMachine = $this->stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH);
        $stateMachine->apply($transition);
    }
}
