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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandProvider;

use Sylius\Bundle\CoreBundle\PaymentRequest\Checker\PaymentRequestDuplicationCheckerInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Provider\GatewayFactoryNameProviderInterface;
use Sylius\Bundle\PaymentBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class GatewayFactoryCommandProvider extends AbstractServiceCommandProvider
{
    /**
     * @param ServiceProviderInterface<PaymentRequestCommandProviderInterface> $locator
     */
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

    protected function getCommandProviderIndex(PaymentRequestInterface $paymentRequest): string {
        return $this->gatewayFactoryNameProvider->provideFromPaymentRequest($paymentRequest);
    }
}
