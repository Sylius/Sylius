<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Payment\Checker;

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Webmozart\Assert\Assert;

final class PaymentRequestDuplicationChecker implements PaymentRequestDuplicationCheckerInterface
{

    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function hasDuplicates(PaymentRequestInterface $paymentRequest): bool
    {
        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod);

        $paymentRequests = $this->paymentRequestRepository->findExisting(
            $payment,
            $paymentMethod,
            $paymentRequest->getType()
        );

        return count($paymentRequests) > 0;
    }
}
