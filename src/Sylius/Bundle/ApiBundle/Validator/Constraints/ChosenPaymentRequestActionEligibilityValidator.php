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

use Sylius\Bundle\PaymentBundle\Command\AddPaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PaymentBundle\CommandProvider\ServiceProviderAwareCommandProviderInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Provider\GatewayFactoryNameProviderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChosenPaymentRequestActionEligibilityValidator extends ConstraintValidator
{
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

        $gatewayFactoryCommandProvider = $this->getCommandProvider($value);
        if (null === $gatewayFactoryCommandProvider) {
            $this->context->addViolation($constraint->notAvailable, [
                '%code%' => $value->getPaymentMethodCode(),
                '%id%' => $value->getPaymentId(),
            ]);
        }

        if (false === $gatewayFactoryCommandProvider instanceof ServiceProviderAwareCommandProviderInterface) {
            return;
        }

        $actionsCommandProvider = $gatewayFactoryCommandProvider->getCommandProvider($value->getAction());
        if (null !== $actionsCommandProvider) {
            return;
        }

        $availableActions = $gatewayFactoryCommandProvider->getCommandProviderIndex();

        $this->context->addViolation($constraint->notAvailable, ['%actions%' => implode(', ', $availableActions)]);
    }

    private function getCommandProvider(AddPaymentRequest $addPaymentRequest): ?PaymentRequestCommandProviderInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $addPaymentRequest->getPaymentMethodCode()]);

        if ($paymentMethod === null) {
            return null;
        }

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            return null;
        }

        $factoryName = $this->gatewayFactoryNameProvider->provide($paymentMethod);
        $commandProvider = $this->gatewayFactoryCommandProvider->getCommandProvider($factoryName);

        return $commandProvider ?? null;
    }
}
