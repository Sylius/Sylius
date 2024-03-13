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

namespace spec\Sylius\Component\Payment\Canceller;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PaymentRequestCancellerSpec extends ObjectBehavior
{
    function let(PaymentRequestRepositoryInterface $paymentRequestRepository): void
    {
        $this->beConstructedWith($paymentRequestRepository);
    }

    function it_cancels_payment_requests_if_the_payment_method_code_is_different(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        PaymentRequestInterface $paymentRequest1,
        PaymentRequestInterface $paymentRequest2,
        PaymentMethodInterface $paymentMethod1,
        PaymentMethodInterface $paymentMethod2,
    ): void {
        $paymentRequestRepository->findByStatesAndPaymentId([PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING], 1)
            ->willReturn([$paymentRequest1, $paymentRequest2]);

        $paymentRequest1->getMethod()->willReturn($paymentMethod1);
        $paymentMethod1->getCode()->willReturn('payment_method_with_different_code');
        $paymentRequest2->getMethod()->willReturn($paymentMethod2);
        $paymentMethod2->getCode()->willReturn('payment_method_code');

        $paymentRequest1->setState(PaymentRequestInterface::STATE_CANCELLED)->shouldBeCalled();
        $paymentRequest2->setState(PaymentRequestInterface::STATE_CANCELLED)->shouldNotBeCalled();

        $this->cancelPaymentRequests(1, 'payment_method_code');
    }

    function it_does_not_cancel_payment_requests_if_none_found(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
    ): void {
        $paymentRequestRepository->findByStatesAndPaymentId([PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING], 1)
            ->willReturn([]);

        $this->cancelPaymentRequests(1, 'payment_method_code');
    }
}
