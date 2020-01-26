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

namespace Sylius\Bundle\AdminBundle\Shipper;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderShipmentShipper implements OrderShipmentShipperInterface
{
    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var ObjectManager */
    private $shipmentManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        StateMachineFactoryInterface $stateMachineFactory,
        ObjectManager $shipmentManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->shipmentManager = $shipmentManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function ship(ShipmentInterface $shipment, string $trackingCode = null): void
    {
        $stateMachine = $this->stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH);

        if (!$stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)) {
            throw new UpdateHandlingException();
        }

        if ($trackingCode !== null) {
            $shipment->setTracking($trackingCode);
        }
        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP);

        $this->eventDispatcher->dispatch('sylius.shipment.post_ship', new GenericEvent($shipment));

        $this->shipmentManager->flush();
    }
}
