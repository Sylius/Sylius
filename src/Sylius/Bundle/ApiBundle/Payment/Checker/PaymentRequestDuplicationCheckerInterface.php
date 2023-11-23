<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Payment\Checker;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestDuplicationCheckerInterface
{
    public function hasDuplicates(PaymentRequestInterface $paymentRequest): bool;
}
