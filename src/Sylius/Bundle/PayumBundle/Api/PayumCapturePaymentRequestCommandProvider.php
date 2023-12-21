<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Api;

use Sylius\Bundle\ApiBundle\Payment\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PayumBundle\Command\CapturePaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PayumCapturePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getType() === PaymentRequestInterface::DATA_TYPE_CAPTURE;
    }

    public function handle(PaymentRequestInterface $paymentRequest): object
    {
        return new CapturePaymentRequest($paymentRequest->getHash());
    }
}
