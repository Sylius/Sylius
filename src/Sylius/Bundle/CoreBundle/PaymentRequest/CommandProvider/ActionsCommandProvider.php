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
        $action = $paymentRequest->getAction();
        $commandProvider = $this->getCommandProvider($action);
        if (null === $commandProvider) {
            return false;
        }

        return $commandProvider->supports($paymentRequest);
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        $action = $paymentRequest->getAction();
        $commandProvider = $this->locator->get($action);
        if (null === $commandProvider) {
            throw new PaymentRequestNotSupportedException(sprintf(
                'No payment request command provider supported for this action "%s".',
                $action
            ));
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
