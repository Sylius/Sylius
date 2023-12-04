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

namespace Sylius\Bundle\ShopBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface as DecoratedOrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

// This whole class needs to go
final class OrderEmailManager implements OrderEmailManagerInterface
{
    public function __construct(
        private SenderInterface $emailSender,
        private ?DecoratedOrderEmailManagerInterface $decoratedEmailManager,
    ) {
        if ($decoratedEmailManager === null) {
            trigger_deprecation(
                'sylius/shop-bundle',
                '1.8',
                'Not passing an instance of %s to %s constructor is deprecated and will be removed in Sylius 2.0.',
                DecoratedOrderEmailManagerInterface::class,
                self::class,
            );
        }
    }

    public function sendConfirmationEmail(OrderInterface $order): void
    {
        if ($this->decoratedEmailManager !== null) {
            $this->decoratedEmailManager->sendConfirmationEmail($order);

            return;
        }

        $email = $order->getCustomer()->getEmail();
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
}
