<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestDuplicationCheckerInterface
{
    public function hasDuplicates(PaymentRequestInterface $paymentRequest): bool;
}
