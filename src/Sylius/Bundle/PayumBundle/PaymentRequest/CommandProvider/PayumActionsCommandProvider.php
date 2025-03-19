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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\CommandProvider;

use Sylius\Bundle\PaymentBundle\CommandProvider\AbstractServiceCommandProvider;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

/** @experimental */
final class PayumActionsCommandProvider extends AbstractServiceCommandProvider
{
    /** @param ServiceProviderInterface<PaymentRequestCommandProviderInterface> $locator */
    public function __construct(
        private AbstractServiceCommandProvider $decoratedActionsCommandProvider,
        protected ServiceProviderInterface $locator,
    ) {
    }

    protected function getCommandProviderIndex(PaymentRequestInterface $paymentRequest): string
    {
        return $paymentRequest->getAction();
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentRequest->getMethod()->getGatewayConfig();

        return $gatewayConfig?->getUsePayum() ?? true
            ? parent::provide($paymentRequest)
            : $this->decoratedActionsCommandProvider->provide($paymentRequest);
    }
}
