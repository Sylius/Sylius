<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class UpdatePaymentRequestEligibilityCheckerSpec extends ObjectBehavior
{
    function it_returns_true_if_payment_request_is_in_new_state(
        PaymentRequestInterface $paymentRequest
    ): void
    {
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_NEW);
        $this->isEligible($paymentRequest)->shouldReturn(true);
    }

    function it_returns_true_if_payment_request_is_in_processing_state(
        PaymentRequestInterface $paymentRequest
    ): void
    {
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_PROCESSING);
        $this->isEligible($paymentRequest)->shouldReturn(true);
    }

    function it_returns_false_if_payment_request_is_not_in_completed_state(
        PaymentRequestInterface $paymentRequest
    ): void
    {
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_COMPLETED);
        $this->isEligible($paymentRequest)->shouldReturn(false);
    }

    function it_returns_false_if_payment_request_is_not_in_failed_state(
        PaymentRequestInterface $paymentRequest
    ): void
    {
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_FAILED);
        $this->isEligible($paymentRequest)->shouldReturn(false);
    }

    function it_returns_false_if_payment_request_is_not_in_cancelled_state(
        PaymentRequestInterface $paymentRequest
    ): void
    {
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_CANCELLED);
        $this->isEligible($paymentRequest)->shouldReturn(false);
    }
}
