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

/** @experimental */
final class GatewayFactoryNameProvider implements GatewayFactoryNameProviderInterface
{
    public function provide(PaymentMethodInterface $paymentMethod): string
    {
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        return $gatewayConfig->getConfig()['factory'] ?? $gatewayConfig->getFactoryName();
    }

    public function provideFromPaymentRequest(PaymentRequestInterface $paymentRequest): string
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        return $this->provide($paymentMethod);
    }
}
