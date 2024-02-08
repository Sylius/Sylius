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

use Sylius\Bundle\ApiBundle\Command\Checkout\SendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class SendShipmentConfirmationEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private SenderInterface $emailSender,
        private ShipmentRepositoryInterface $shipmentRepository,
    ) {
    }

    public function __invoke(SendShipmentConfirmationEmail $sendShipmentConfirmationEmail): void
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($sendShipmentConfirmationEmail->shipmentId);
        $order = $shipment->getOrder();
        Assert::notNull($order);

        $email = $order->getCustomer()->getEmail();
        Assert::notNull($email);

        $this->emailSender->send(
            Emails::SHIPMENT_CONFIRMATION,
            [$email],
            [
                'shipment' => $shipment,
                'order' => $order,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ],
        );
    }
}
