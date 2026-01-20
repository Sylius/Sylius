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

namespace Sylius\Bundle\CoreBundle\OrderPay\Action;

use Sylius\Bundle\CoreBundle\OrderPay\Provider\UrlProviderInterface;
use Sylius\Bundle\PaymentBundle\Processor\HttpResponseProcessorInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Sylius\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @experimental */
final class PaymentRequestPayAction
{
    /**
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private MetadataInterface $paymentRequestMetadata,
        private RequestConfigurationFactoryInterface $requestConfigurationFactory,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private HttpResponseProcessorInterface $httpResponseProcessor,
        private UrlProviderInterface $afterPayUrlProvider,
    ) {
    }

    public function __invoke(Request $request, string $hash): Response
    {
        $requestConfiguration = $this->requestConfigurationFactory->create($this->paymentRequestMetadata, $request);

        $paymentRequest = $this->paymentRequestRepository->find($hash);

        if (null === $paymentRequest) {
            throw new NotFoundHttpException(sprintf('No payment request found with hash "%s".', $hash));
        }

        $response = $this->httpResponseProcessor->process($requestConfiguration, $paymentRequest);

        return $response ?? new RedirectResponse($this->afterPayUrlProvider->getUrl($paymentRequest));
    }
}
