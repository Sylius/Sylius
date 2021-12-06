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
use Sylius\Component\Mailer\Sender\SenderInterface;

final class OrderEmailManager implements OrderEmailManagerInterface
{
    private SenderInterface $emailSender;

    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function sendConfirmationEmail(OrderInterface $order): void
    {
        $this->emailSender->send(
            Emails::ORDER_CONFIRMATION_RESENT,
            [$order->getCustomer()->getEmail()],
            [
                'order' => $order,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ]
        );
    }
}
