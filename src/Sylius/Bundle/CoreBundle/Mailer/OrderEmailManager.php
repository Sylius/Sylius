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

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

final class OrderEmailManager implements OrderEmailManagerInterface
{
    public function __construct(private SenderInterface $emailSender)
    {
    }

    public function sendConfirmationEmail(OrderInterface $order): void
    {
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        $email = $customer->getEmail();
        Assert::notNull($email);

        $this->emailSender->send(
            Emails::ORDER_CONFIRMATION,
            [$email],
            [
                'order' => $order,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ],
        );
    }

    public function resendConfirmationEmail(OrderInterface $order): void
    {
        $email = $order->getCustomer()->getEmail();
        Assert::notNull($email);

        $this->emailSender->send(
            Emails::ORDER_CONFIRMATION_RESENT,
            [$email],
            [
                'order' => $order,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ],
        );
    }
}
