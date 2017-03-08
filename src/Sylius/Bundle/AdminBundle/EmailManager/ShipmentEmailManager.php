<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * @author Hussein Jafferjee <hussein@jafferjee.ca>
 */
final class ShipmentEmailManager implements ShipmentEmailManagerInterface
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmail(ShipmentInterface $shipment)
    {
        /** @var OrderInterface $order */
        $order = $shipment->getOrder();

        $this->emailSender->send(Emails::SHIPMENT_CONFIRMATION, [$order->getCustomer()->getEmail()], [
            'shipment' => $shipment,
            'order' => $order,
        ]);
    }
}
