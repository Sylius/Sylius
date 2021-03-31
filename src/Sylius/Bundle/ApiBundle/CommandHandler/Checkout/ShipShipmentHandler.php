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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ShipShipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShipShipmentHandler implements MessageHandlerInterface
{
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        FactoryInterface $stateMachineFactory
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(ShipShipment $shipShipment): void
    {
        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($shipShipment->shipmentId);

        Assert::notNull($shipment,'Shipment has has not been found.');

        if ($shipShipment->tracking !== null) {
            $shipment->setTracking($shipShipment->tracking);
        }

        $stateMachine = $this->stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(ShipmentTransitions::TRANSITION_SHIP),
            'This shipment cannot be completed.'
        );

        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP);
    }
}
