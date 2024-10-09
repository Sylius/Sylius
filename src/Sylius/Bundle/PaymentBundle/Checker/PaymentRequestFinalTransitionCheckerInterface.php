<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestFinalTransitionCheckerInterface
{
    public function isFinal(PaymentRequestInterface $paymentRequest): bool;
}
