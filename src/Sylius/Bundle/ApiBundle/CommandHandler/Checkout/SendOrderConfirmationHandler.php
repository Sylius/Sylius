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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class SendOrderConfirmationHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderEmailManagerInterface $orderEmailManager,
    ) {
    }

    public function __invoke(SendOrderConfirmation $sendOrderConfirmation): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByTokenValue($sendOrderConfirmation->orderToken());
        $email = $order->getCustomer()->getEmail();
        Assert::notNull($email);

        $this->orderEmailManager->sendConfirmationEmail($order);
    }
}
