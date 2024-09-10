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

use Sylius\Bundle\PaymentBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class ActionsCommandProvider implements ServiceProviderAwareCommandProviderInterface
{
    public function __construct(
        private ServiceProviderInterface $locator,
    ) {
    }

    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        $commandProvider = $this->locator->get($paymentRequest->getAction());
        if (null === $commandProvider) {
            return false;
        }

        return $commandProvider->supports($paymentRequest);
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        $commandProvider = $this->locator->get($paymentRequest->getAction());
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
}
