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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChosenPaymentMethodEligibilityValidator extends ConstraintValidator
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentMethodsResolverInterface $paymentMethodsResolver
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ChoosePaymentMethod::class);

        /** @var ChosenPaymentMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, ChosenPaymentMethodEligibility::class);

        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $value->getPaymentMethodCode()]);

        if ($paymentMethod === null) {
            $this->context->addViolation($constraint->notExist, ['%code%' => $value->getPaymentMethodCode()]);

            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $this->paymentRepository->find($value->paymentId);
        Assert::notNull($payment);

        if (!in_array($paymentMethod, $this->paymentMethodsResolver->getSupportedMethods($payment), true)) {
            $this->context->addViolation($constraint->notAvailable, ['%name%' => $paymentMethod->getName()]);
        }
    }
}
