<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * @author Hussein Jafferjee <hussein@jafferjee.ca>
 */
final class OrderEmailManager implements OrderEmailManagerInterface
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
    public function sendConfirmationEmail(OrderInterface $order)
    {
        $this->emailSender->send(Emails::ORDER_CONFIRMATION, [$order->getCustomer()->getEmail()], ['order' => $order]);
    }
}
