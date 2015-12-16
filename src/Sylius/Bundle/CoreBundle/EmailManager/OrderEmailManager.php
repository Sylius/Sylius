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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * Sends the order confirmation email.
 *
 * @author Hussein Jafferjee <hussein@jafferjee.ca>
 */
class OrderEmailManager
{
    /** @var SenderInterface */
    protected $emailSender;

    /**
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param OrderInterface $order
     */
    public function sendConfirmationEmail(OrderInterface $order)
    {
        $this->emailSender->send(Emails::ORDER_CONFIRMATION, array($order->getCustomer()->getEmail()), array('order' => $order));
    }
}
