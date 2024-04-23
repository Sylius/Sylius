<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class CreateShipmentListener
{
    public function __construct(private StateMachineInterface $compositeStateMachine)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $shipments = $order->getShipments();

        foreach ($shipments as $shipment) {
            if ($this->compositeStateMachine->can($shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_CREATE)) {
                $this->compositeStateMachine->apply($shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_CREATE);
            }
        }
    }
}
