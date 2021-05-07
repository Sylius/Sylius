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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\SendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class SendShipmentConfirmationEmailHandler implements MessageHandlerInterface
{
    /** @var SenderInterface */
    private $emailSender;

    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    public function __construct(SenderInterface $emailSender, ShipmentRepositoryInterface $shipmentRepository)
    {
        $this->emailSender = $emailSender;
        $this->shipmentRepository = $shipmentRepository;
    }

    public function __invoke(SendShipmentConfirmationEmail $sendShipmentConfirmationEmail): void
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($sendShipmentConfirmationEmail->shipmentId);

        Assert::notNull($order = $shipment->getOrder());

        $this->emailSender->send(
            Emails::SHIPMENT_CONFIRMATION,
            [$order->getCustomer()->getEmail()],
            [
                'shipment' => $shipment,
                'order' => $order,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ]
        );
    }
}
