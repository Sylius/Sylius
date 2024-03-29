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

trigger_deprecation(
    'sylius/shop-bundle',
    '1.13',
    'The "%s" class is deprecated, use "%s" instead.',
    OrderEmailManager::class,
    \Sylius\Bundle\CoreBundle\Mailer\OrderEmailManager::class,
);

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see \Sylius\Bundle\CoreBundle\Mailer\OrderEmailManager} instead. */
final class OrderEmailManager implements OrderEmailManagerInterface
{
    public function __construct(
        private SenderInterface $emailSender,
        private ?DecoratedOrderEmailManagerInterface $decoratedEmailManager,
    ) {
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
