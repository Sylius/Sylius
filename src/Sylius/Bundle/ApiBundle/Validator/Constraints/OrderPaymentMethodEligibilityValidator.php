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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderPaymentMethodEligibilityValidator extends ConstraintValidator
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        /** @var OrderPaymentMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderPaymentMethodEligibility::class);

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);
        Assert::notNull($order);

        /** @var PaymentInterface $payment */
        foreach ($order->getPayments() as $payment) {
            if (!$payment->getMethod()->isEnabled()) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%paymentMethodName%' => $payment->getMethod()->getName()],
                );
            }
        }
    }
}
