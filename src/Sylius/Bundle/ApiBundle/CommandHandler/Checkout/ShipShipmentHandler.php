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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendShipmentConfirmationEmail;
use Sylius\Bundle\ApiBundle\Command\Checkout\ShipShipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

final class ShipShipmentHandler implements MessageHandlerInterface
{
    public function __construct(
        private ShipmentRepositoryInterface $shipmentRepository,
        private FactoryInterface|StateMachineInterface $stateMachineFactory,
        private MessageBusInterface $eventBus,
    ) {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            trigger_deprecation(
                'sylius/api-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the second argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    FactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function __invoke(ShipShipment $shipShipment): ShipmentInterface
    {
        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($shipShipment->shipmentId);

        Assert::notNull($shipment, 'Shipment has not been found.');

        if ($shipShipment->trackingCode !== null) {
            $shipment->setTracking($shipShipment->trackingCode);
        }

        $stateMachine = $this->getStateMachine();
        Assert::true(
            $stateMachine->can($shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP),
            'This shipment cannot be completed.',
        );

        $stateMachine->apply($shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP);

        $this->eventBus->dispatch(new SendShipmentConfirmationEmail($shipShipment->shipmentId), [new DispatchAfterCurrentBusStamp()]);

        return $shipment;
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
