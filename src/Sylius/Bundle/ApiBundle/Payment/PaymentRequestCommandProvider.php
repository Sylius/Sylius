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

namespace Sylius\Bundle\ApiBundle\Payment;

use Sylius\Bundle\ApiBundle\Payment\Checker\PaymentRequestDuplicationCheckerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Webmozart\Assert\Assert;

final class PaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function __construct(
        private PaymentRequestDuplicationCheckerInterface $paymentRequestDuplicationChecker,
        private ServiceProviderInterface $paymentRequestCommandProviderLocator,
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

    public function handle(PaymentRequestInterface $paymentRequest): object
    {
        return $this->getCommandProvider($paymentRequest)->handle($paymentRequest);
    }

    protected function getCommandProvider(PaymentRequestInterface $paymentRequest): PaymentRequestCommandProviderInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);
        /** @var PaymentRequestCommandProviderInterface $service */
        $factoryName = $gatewayConfig->getConfig()['factory'] ?? $gatewayConfig->getFactoryName();

        $id = sprintf('%s::%s', $factoryName, $paymentRequest->getType());

        /** @var PaymentRequestCommandProviderInterface $provider */
        $provider = $this->paymentRequestCommandProviderLocator->get($id);

        return $provider;
    }
}
