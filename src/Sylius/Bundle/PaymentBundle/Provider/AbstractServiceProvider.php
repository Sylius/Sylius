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

use Sylius\Bundle\PaymentBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceProviderInterface;

/** @experimental */
abstract class AbstractServiceProvider implements ServiceProviderAwareProviderInterface
{
    /** @var ServiceProviderInterface<HttpResponseProviderInterface> */
    protected ServiceProviderInterface $locator;

    public function supports(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): bool {
        $index = $this->getHttpResponseProviderIndex($paymentRequest);
        $httpResponseProvider = $this->getHttpResponseProvider($index);
        if (null === $httpResponseProvider) {
            return false;
        }

        return $httpResponseProvider->supports($requestConfiguration, $paymentRequest);
    }

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): Response {
        $index = $this->getHttpResponseProviderIndex($paymentRequest);
        $httpResponseProvider = $this->getHttpResponseProvider($index);
        if (null === $httpResponseProvider) {
            throw new PaymentRequestNotSupportedException(sprintf(
                'No PaymentRequest HTTP Response provider supported for "%s" (providers available are: %s).',
                $index,
                implode(', ', $this->getProviderIndexes()),
            ));
        }

        return $httpResponseProvider->getResponse($requestConfiguration, $paymentRequest);
    }

    public function getProviderIndexes(): array
    {
        return array_keys($this->locator->getProvidedServices());
    }

    public function getHttpResponseProvider(string $index): ?HttpResponseProviderInterface
    {
        if (false === $this->locator->has($index)) {
            return null;
        }

        return $this->locator->get($index);
    }

    abstract protected function getHttpResponseProviderIndex(PaymentRequestInterface $paymentRequest): string;
}
