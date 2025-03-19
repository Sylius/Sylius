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

namespace Sylius\Bundle\PaymentBundle\CommandProvider;

use Sylius\Bundle\PaymentBundle\Checker\PaymentRequestDuplicationCheckerInterface;
use Sylius\Bundle\PaymentBundle\Provider\GatewayFactoryNameProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

/** @experimental */
final class GatewayFactoryCommandProvider extends AbstractServiceCommandProvider
{
    /** @param ServiceProviderInterface<PaymentRequestCommandProviderInterface> $locator */
    public function __construct(
        private PaymentRequestDuplicationCheckerInterface $paymentRequestDuplicationChecker,
        private GatewayFactoryNameProviderInterface $gatewayFactoryNameProvider,
        protected ServiceProviderInterface $locator,
    ) {
    }

    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        $hasDuplicates = $this->paymentRequestDuplicationChecker->hasDuplicates($paymentRequest);

        if ($hasDuplicates) {
            return false;
        }

        return parent::supports($paymentRequest);
    }

    protected function getCommandProviderIndex(PaymentRequestInterface $paymentRequest): string
    {
        return $this->gatewayFactoryNameProvider->provideFromPaymentRequest($paymentRequest);
    }
}
