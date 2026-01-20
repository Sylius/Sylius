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

use Sylius\Bundle\PaymentBundle\Normalizer\SymfonyRequestNormalizerInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class NotifyPayloadProcessor implements NotifyPayloadProcessorInterface
{
    public function __construct(
        private SymfonyRequestNormalizerInterface $requestNormalizer,
    ) {
    }

    public function process(PaymentRequestInterface $paymentRequest, Request $request): void
    {
        $requestPayload = $this->requestNormalizer->normalize($request);
        $payload = $paymentRequest->getPayload();

        if (is_array($payload)) {
            $paymentRequest->setPayload(array_merge($payload, $requestPayload));
        }

        if (null === $payload) {
            $paymentRequest->setPayload($requestPayload);
        }
    }
}
