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
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Checker\OrderPaymentMethodPerChannelEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderPaymentMethodPerChannelEligibilityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly OrderPaymentMethodPerChannelEligibilityCheckerInterface $perChannelChecker,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var OrderInterface $value */
        Assert::isInstanceOf($value, OrderInterface::class);

        /** @var OrderPaymentMethodPerChannelEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderPaymentMethodPerChannelEligibility::class);

        $payments = $value->getPayments();

        foreach ($payments as $payment) {
            $paymentMethod = $payment->getMethod();
            if ($paymentMethod instanceof PaymentMethodInterface && !$this->perChannelChecker->isEligible($paymentMethod)) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%paymentMethodName%' => $paymentMethod->getName()],
                );
            }
        }
    }
}
