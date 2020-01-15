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

namespace Sylius\Bundle\AdminBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class ShipmentEmailManager implements ShipmentEmailManagerInterface
{
    /** @var SenderInterface */
    private $emailSender;

    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function sendConfirmationEmail(ShipmentInterface $shipment): void
    {
        /** @var OrderInterface $order */
        $order = $shipment->getOrder();

        $this->emailSender->send(Emails::SHIPMENT_CONFIRMATION, [$order->getCustomer()->getEmail()], [
            'shipment' => $shipment,
            'order' => $order,
            'channel' => $order->getChannel(),
            'localeCode' => $order->getLocaleCode(),
        ]);
    }
}
