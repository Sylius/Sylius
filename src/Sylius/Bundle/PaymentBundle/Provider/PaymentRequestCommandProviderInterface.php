<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Provider;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool;

    public function provide(PaymentRequestInterface $paymentRequest): object;
}
