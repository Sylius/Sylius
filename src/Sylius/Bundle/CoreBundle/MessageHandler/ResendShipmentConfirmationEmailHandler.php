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

namespace Sylius\Bundle\CoreBundle\MessageHandler;

use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Message\ResendShipmentConfirmationEmail;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResendShipmentConfirmationEmailHandler implements MessageHandlerInterface
{
    /**
     * @param RepositoryInterface<ShipmentInterface> $shipmentRepository
     */
    public function __construct(
        private RepositoryInterface $shipmentRepository,
        private ShipmentEmailManagerInterface $shipmentEmailManager,
    ) {
    }

    public function __invoke(ResendShipmentConfirmationEmail $resendShipmentConfirmationEmail): void
    {
        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($resendShipmentConfirmationEmail->getShipmentId());
        if (null === $shipment) {
            throw new NotFoundHttpException(sprintf('Shipment with id "%s" does not exist.', $resendShipmentConfirmationEmail->getShipmentId()));
        }

        $this->shipmentEmailManager->resendConfirmationEmail($shipment);
    }
}
