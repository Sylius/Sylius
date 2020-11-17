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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\SendOrderConfirmation;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class SendOrderConfirmationHandler implements MessageHandlerInterface
{
    /** @var SenderInterface */
    private $emailSender;

    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function __invoke(SendOrderConfirmation $sendOrderConfirmation): void
    {
        $order = $sendOrderConfirmation->order();

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
