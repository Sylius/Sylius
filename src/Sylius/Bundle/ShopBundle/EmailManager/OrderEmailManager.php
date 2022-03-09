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

namespace Sylius\Bundle\ShopBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface as DecoratedOrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class OrderEmailManager implements OrderEmailManagerInterface
{
    public function __construct(
        private SenderInterface $emailSender,
        private ?DecoratedOrderEmailManagerInterface $decoratedEmailManager
    ) {
        if ($decoratedEmailManager === null) {
            @trigger_error(
                sprintf(
                    'Not passing an instance of %s to %s constructor is deprecated since Sylius 1.8 and will be removed in Sylius 2.0.',
                    DecoratedOrderEmailManagerInterface::class,
                    self::class
                ),
                \E_USER_DEPRECATED
            );
        }
    }

    public function sendConfirmationEmail(OrderInterface $order): void
    {
        if ($this->decoratedEmailManager !== null) {
            $this->decoratedEmailManager->sendConfirmationEmail($order);

            return;
        }

        $this->emailSender->send(
            Emails::ORDER_CONFIRMATION,
            [$order->getCustomer()->getEmail()],
            [
                'order' => $order,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ]
        )
        ;
    }
}
