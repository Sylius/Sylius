<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PaymentRequestDuplicationChecker implements PaymentRequestDuplicationCheckerInterface
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function hasDuplicates(PaymentRequestInterface $paymentRequest): bool
    {
        $paymentRequests = $this->paymentRequestRepository->findOtherExisting($paymentRequest);

        return count($paymentRequests) > 0;
    }
}
