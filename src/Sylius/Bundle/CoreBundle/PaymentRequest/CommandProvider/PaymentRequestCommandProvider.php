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
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Webmozart\Assert\Assert;

final class PaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function __construct(
        private PaymentRequestDuplicationCheckerInterface $paymentRequestDuplicationChecker,
        /** @var ServiceProviderInterface<PaymentRequestCommandProviderInterface> */
        private ServiceProviderInterface $locator,
    ) {
    }

    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        $hasDuplicates = $this->paymentRequestDuplicationChecker->hasDuplicates($paymentRequest);

        if ($hasDuplicates) {
            return false;
        }

        return $this->getCommandProvider($paymentRequest)->supports($paymentRequest);
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return $this->getCommandProvider($paymentRequest)->provide($paymentRequest);
    }

    private function getCommandProvider(PaymentRequestInterface $paymentRequest): PaymentRequestCommandProviderInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);
        $factoryName = $gatewayConfig->getConfig()['factory'] ?? $gatewayConfig->getFactoryName();

        return $this->locator->get($factoryName);
    }
}
