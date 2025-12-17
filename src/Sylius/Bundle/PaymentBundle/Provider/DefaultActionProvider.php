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

namespace Sylius\Bundle\PaymentBundle\Provider;

use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

/** @experimental */
final readonly class DefaultActionProvider implements DefaultActionProviderInterface
{
    /**
     * @param PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository
     */
    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private string $defaultAction,
    ) {
    }

    public function getAction(PaymentRequestInterface $paymentRequest): string
    {
        $paymentMethod = $paymentRequest->getMethod();

        return $this->getActionFromPaymentMethod($paymentMethod, $paymentRequest->getAction());
    }

    public function getActionFromPaymentMethodCode(string $paymentMethodCode, ?string $defaultAction = null): string
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $paymentMethodCode]);
        if (null === $paymentMethod) {
            return $defaultAction ?? $this->defaultAction;
        }

        return $this->getActionFromPaymentMethod($paymentMethod, $defaultAction);
    }

    public function getActionFromPaymentMethod(PaymentMethodInterface $paymentMethod, ?string $defaultAction = null): string
    {
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        $authorize = $gatewayConfig->getConfig()['use_authorize'] ?? false;

        return $authorize ? PaymentRequestInterface::ACTION_AUTHORIZE : $defaultAction ?? $this->defaultAction;
    }
}
