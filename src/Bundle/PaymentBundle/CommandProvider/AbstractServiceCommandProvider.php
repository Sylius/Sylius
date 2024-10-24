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

use Sylius\Bundle\PaymentBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

/** @experimental */
abstract class AbstractServiceCommandProvider implements ServiceProviderAwareCommandProviderInterface
{
    /** @var ServiceProviderInterface<PaymentRequestCommandProviderInterface> */
    protected ServiceProviderInterface $locator;

    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        $index = $this->getCommandProviderIndex($paymentRequest);
        $httpResponseProvider = $this->getCommandProvider($index);
        if (null === $httpResponseProvider) {
            return false;
        }

        return $httpResponseProvider->supports($paymentRequest);
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        $index = $this->getCommandProviderIndex($paymentRequest);
        $commandProvider = $this->getCommandProvider($index);
        if (null === $commandProvider) {
            throw new PaymentRequestNotSupportedException(sprintf(
                'No payment request command provider supported for "%s" (command providers available are: %s).',
                $index,
                implode(', ', $this->getCommandProviderIndexes()),
            ));
        }

        return $commandProvider->provide($paymentRequest);
    }

    public function getCommandProvider(string $index): ?PaymentRequestCommandProviderInterface
    {
        if (false === $this->locator->has($index)) {
            return null;
        }

        return $this->locator->get($index);
    }

    public function getCommandProviderIndexes(): array
    {
        return array_keys($this->locator->getProvidedServices());
    }

    abstract protected function getCommandProviderIndex(PaymentRequestInterface $paymentRequest): string;
}
