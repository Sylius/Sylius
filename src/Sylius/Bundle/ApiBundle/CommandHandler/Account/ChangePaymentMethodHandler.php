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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Account;

use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class ChangePaymentMethodHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentMethodChangerInterface $paymentMethodChanger,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(ChangePaymentMethod $changePaymentMethod): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $changePaymentMethod->orderTokenValue]);

        Assert::notNull($order, 'Order has not been found.');

        return $this->paymentMethodChanger->changePaymentMethod(
            $changePaymentMethod->paymentMethodCode,
            $changePaymentMethod->paymentId,
            $order,
        );
    }
}
