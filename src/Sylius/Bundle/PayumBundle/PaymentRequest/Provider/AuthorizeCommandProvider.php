<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Provider;

use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PayumBundle\Command\AuthorizePaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class AuthorizeCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getType() === PaymentRequestInterface::DATA_TYPE_AUTHORIZE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new AuthorizePaymentRequest($paymentRequest->getHash());
    }
}
