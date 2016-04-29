<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Hussein Jafferjee <hussein@jafferjee.ca>
 */
class ShipmentEmailManager
{
    /**
     * @var SenderInterface
     */
    protected $emailSender;

    /**
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param ShipmentInterface $shipment
     */
    public function sendConfirmationEmail(ShipmentInterface $shipment)
    {
        /** @var \Sylius\Component\Core\Model\OrderInterface $order */
        $order = $shipment->getOrder();

        $this->emailSender->send(Emails::SHIPMENT_CONFIRMATION, [$order->getCustomer()->getEmail()], [
            'shipment' => $shipment,
            'order' => $order,
        ]);
    }
}
