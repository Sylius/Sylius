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
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class ActionsHttpResponseProvider implements ServiceProviderAwareProviderInterface
{
    public function __construct(
        private ServiceProviderInterface $locator,
    ) {
    }

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): Response {
        $action = $paymentRequest->getAction();
        $httpResponseProvider = $this->getHttpResponseProvider($action);
        if (null === $httpResponseProvider) {
            throw new PaymentRequestNotSupportedException(sprintf(
                'No payment request HTTP Response provider supported for this action "%s".',
                $action
            ));
        }

        return $httpResponseProvider->getResponse($requestConfiguration, $paymentRequest);
    }

    public function supports(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): bool {
        $action = $paymentRequest->getAction();
        $httpResponseProvider = $this->getHttpResponseProvider($action);
        if (null === $httpResponseProvider) {
            return false;
        }

        return $httpResponseProvider->supports($requestConfiguration, $paymentRequest);
    }

    public function getProviderIndex(): array {
        return $this->locator->getProvidedServices();
    }

    public function getHttpResponseProvider(string $index): ?HttpResponseProviderInterface
    {
        return $this->locator->get($index);
    }
}
