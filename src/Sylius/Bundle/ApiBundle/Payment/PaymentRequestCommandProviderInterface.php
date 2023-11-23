<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Payment;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool;

    public function handle(PaymentRequestInterface $paymentRequest): object;
}
