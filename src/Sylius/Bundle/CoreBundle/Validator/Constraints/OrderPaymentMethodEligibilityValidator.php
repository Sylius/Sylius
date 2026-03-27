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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderPaymentMethodEligibilityValidator extends ConstraintValidator
{
    /**
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var OrderInterface $value */
        Assert::isInstanceOf($value, OrderInterface::class);

        /** @var OrderPaymentMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderPaymentMethodEligibility::class);

        $channel = $value->getChannel();
        Assert::notNull($channel);

        $payments = $value->getPayments();

        /** @var PaymentInterface $payment */
        foreach ($payments as $payment) {
            /** @var ?PaymentMethodInterface $paymentMethod */
            $paymentMethod = $payment->getMethod();

            if (null === $paymentMethod) {
                continue;
            }

            if (!$payment->getMethod()->isEnabled() || !$paymentMethod->hasChannel($channel)) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%paymentMethodName%' => $payment->getMethod()->getName()],
                );
            }
        }
    }
}
