<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Provider;

use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PayumBundle\Command\StatusPaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class StatusCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getType() === PaymentRequestInterface::DATA_TYPE_STATUS;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new StatusPaymentRequest($paymentRequest->getHash());
    }
}
