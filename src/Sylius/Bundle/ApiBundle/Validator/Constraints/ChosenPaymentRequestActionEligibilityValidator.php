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

use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\ServiceProviderAwareCommandProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\GatewayFactoryNameProviderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChosenPaymentRequestActionEligibilityValidator extends ConstraintValidator
{
    /**
     * @param PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository
     */
    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private ServiceProviderAwareCommandProviderInterface $gatewayFactoryCommandProvider,
        private GatewayFactoryNameProviderInterface $gatewayFactoryNameProvider,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddPaymentRequest::class);

        /** @var ChosenPaymentRequestActionEligibility $constraint */
        Assert::isInstanceOf($constraint, ChosenPaymentRequestActionEligibility::class);

        if (null === $value->action) {
            return;
        }

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $value->paymentMethodCode]);
        if ($paymentMethod?->getGatewayConfig() === null) {
            $this->context->addViolation($constraint->notExist, ['%code%' => $value->paymentMethodCode]);

            return;
        }

        $factoryName = $this->gatewayFactoryNameProvider->provide($paymentMethod);
        $gatewayFactoryCommandProvider = $this->gatewayFactoryCommandProvider->getCommandProvider($factoryName);
        if (null === $gatewayFactoryCommandProvider) {
            $this->context->addViolation($constraint->notAvailable, [
                '%code%' => $value->paymentMethodCode,
                '%id%' => $value->paymentId,
            ]);
        }

        if (false === $gatewayFactoryCommandProvider instanceof ServiceProviderAwareCommandProviderInterface) {
            return;
        }

        $actionsCommandProvider = $gatewayFactoryCommandProvider->getCommandProvider($value->action);
        if (null !== $actionsCommandProvider) {
            return;
        }

        $this->context->addViolation($constraint->notAvailable, [
            '%code%' => $value->paymentMethodCode,
            '%id%' => $value->paymentId,
        ]);
    }
}
