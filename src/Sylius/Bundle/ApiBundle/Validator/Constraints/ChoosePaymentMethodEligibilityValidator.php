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

use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChoosePaymentMethodEligibilityValidator extends ConstraintValidator
{
    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, PaymentMethodCodeAwareInterface::class);

        /** @var ChoosePaymentMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, ChoosePaymentMethodEligibility::class);

        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $value->getPaymentMethodCode()]);

        if ($paymentMethod === null) {
            $this->context->addViolation(
                $constraint->paymentMethodNotExistMessage,
                ['%paymentMethodCode%' => $value->getPaymentMethodCode()]
            );

            return;
        }
    }
}
