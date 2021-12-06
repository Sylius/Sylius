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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class SendOrderConfirmationHandler implements MessageHandlerInterface
{
    private SenderInterface $emailSender;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(SenderInterface $emailSender, OrderRepositoryInterface $orderRepository)
    {
        $this->emailSender = $emailSender;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(SendOrderConfirmation $sendOrderConfirmation): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByTokenValue($sendOrderConfirmation->orderToken());

        $this->emailSender->send(
            Emails::ORDER_CONFIRMATION,
            [$order->getCustomer()->getEmail()],
            [
                'order' => $order,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ]
        );
    }
}
