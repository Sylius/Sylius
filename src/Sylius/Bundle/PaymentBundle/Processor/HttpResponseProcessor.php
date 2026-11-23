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

namespace Sylius\Bundle\PaymentBundle\Processor;

use Sylius\Bundle\PaymentBundle\Announcer\PaymentRequestAnnouncerInterface;
use Sylius\Bundle\PaymentBundle\Provider\ServiceProviderAwareProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class HttpResponseProcessor implements HttpResponseProcessorInterface
{
    public function __construct(
        private PaymentRequestAnnouncerInterface $paymentRequestAnnouncer,
        private ServiceProviderAwareProviderInterface $httpResponseProvider,
    ) {
    }

    public function process(
        Request $request,
        PaymentRequestInterface $paymentRequest,
    ): ?Response {
        $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);

        if ($this->httpResponseProvider->supports($request, $paymentRequest)) {
            return $this->httpResponseProvider->getResponse($request, $paymentRequest);
        }

        return null;
    }
}
