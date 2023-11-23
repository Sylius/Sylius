<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Payment;

use Sylius\Bundle\ApiBundle\Command\Payment\Payum\PayumCapture;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PayumCapturePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getType() === PaymentRequestInterface::DATA_TYPE_CAPTURE;
    }

    public function handle(PaymentRequestInterface $paymentRequest): object
    {
        return new PayumCapture($paymentRequest->getHash());
    }
}
