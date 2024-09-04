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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Provider;

use Sylius\Bundle\PaymentBundle\Exception\PaymentRequestNotSupportedException;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class GatewayFactoryHttpResponseProvider implements ServiceProviderAwareProviderInterface
{
    /**
     * @param ServiceProviderInterface<HttpResponseProviderInterface> $locator
     */
    public function __construct(
        private GatewayFactoryNameProviderInterface $gatewayFactoryNameProvider,
        private ServiceProviderInterface $locator,
    ) {
    }

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): Response {
        $factoryName = $this->getFactoryName($paymentRequest);

        $httpResponseProvider = $this->getHttpResponseProvider($factoryName);

        if (null === $httpResponseProvider) {
            throw new PaymentRequestNotSupportedException(sprintf(
                'No payment request HTTP Response provider supported for the payment method factory name "%s".',
                $factoryName
            ));
        }

        return $httpResponseProvider->getResponse($requestConfiguration, $paymentRequest);
    }

    public function supports(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): bool {
        $factoryName = $this->getFactoryName($paymentRequest);
        $httpResponseProvider = $this->getHttpResponseProvider($factoryName);

        if (null === $httpResponseProvider) {
            return false;
        }

        return $httpResponseProvider->supports($requestConfiguration, $paymentRequest);
    }

    public function getHttpResponseProvider(string $index): ?HttpResponseProviderInterface
    {
        return $this->locator->get($index);
    }

    public function getProviderIndex(): array {
        return $this->locator->getProvidedServices();
    }

    private function getFactoryName(PaymentRequestInterface $paymentRequest): string
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        return $this->gatewayFactoryNameProvider->provide($paymentMethod);
    }
}
