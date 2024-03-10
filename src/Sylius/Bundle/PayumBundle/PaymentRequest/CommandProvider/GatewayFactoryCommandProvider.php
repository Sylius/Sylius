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

use Sylius\Bundle\PaymentBundle\Checker\PaymentRequestDuplicationCheckerInterface;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PaymentBundle\CommandProvider\ServiceProviderAwareCommandProviderInterface;
use Sylius\Bundle\PaymentBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Bundle\PayumBundle\PaymentRequest\Provider\GatewayFactoryNameProviderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Webmozart\Assert\Assert;

final class GatewayFactoryCommandProvider implements ServiceProviderAwareCommandProviderInterface
{
    public function __construct(
        private PaymentRequestDuplicationCheckerInterface $paymentRequestDuplicationChecker,
        private GatewayFactoryNameProviderInterface $gatewayFactoryNameProvider,
        private ServiceProviderInterface $locator,
    ) {
    }

    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        $hasDuplicates = $this->paymentRequestDuplicationChecker->hasDuplicates($paymentRequest);

        if ($hasDuplicates) {
            return false;
        }

        $factoryName = $this->getFactoryName($paymentRequest);
        $commandProvider = $this->getCommandProvider($factoryName);
        if (null === $commandProvider) {
            return false;
        }

        return $commandProvider->supports($paymentRequest);
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        $factoryName = $this->getFactoryName($paymentRequest);
        $commandProvider = $this->getCommandProvider($factoryName);
        if (null === $commandProvider) {
            throw new PaymentRequestNotSupportedException();
        }

        return $commandProvider->provide($paymentRequest);
    }

    public function getCommandProvider(string $index): ?PaymentRequestCommandProviderInterface
    {
        return $this->locator->get($index);
    }

    public function getCommandProviderIndex(): array
    {
        return $this->locator->getProvidedServices();
    }

    private function getFactoryName(PaymentRequestInterface $paymentRequest): string
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        $factoryName = $this->gatewayFactoryNameProvider->provide($paymentMethod);
        Assert::notNull($factoryName, 'Gateway config cannot be null.');

        return $factoryName;
    }
}
