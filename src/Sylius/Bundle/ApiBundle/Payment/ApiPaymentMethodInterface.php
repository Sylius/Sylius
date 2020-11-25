<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Payment;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface ApiPaymentMethodInterface
{
    public function supports(PaymentMethodInterface $paymentMethod): bool;

    public function provideConfiguration(PaymentInterface $payment): array;
}
