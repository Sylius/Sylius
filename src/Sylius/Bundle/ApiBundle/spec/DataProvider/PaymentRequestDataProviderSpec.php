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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Checker\UpdatePaymentRequestEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class PaymentRequestDataProviderSpec extends ObjectBehavior
{
    function let(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        UpdatePaymentRequestEligibilityCheckerInterface $updatePaymentRequestEligibilityChecker,
    ): void {
        $this->beConstructedWith($paymentRequestRepository, $updatePaymentRequestEligibilityChecker);
    }

    function it_does_not_support_not_payment_request_resource(): void
    {
        $this
            ->supports(ProductInterface::class, Request::METHOD_PUT, [])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_not_put_request_method_type(): void
    {
        $this
            ->supports(PaymentRequestInterface::class, 'shop_get', [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET])
            ->shouldReturn(false)
        ;
    }

    function it_supports_payment_request_resource_and_put_request_method_type(): void
    {
        $this
            ->supports(PaymentRequestInterface::class, 'shop_get', [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_PUT])
            ->shouldReturn(true)
        ;
    }

    function it_providers_null_when_update_payment_request_is_not_eligible(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        UpdatePaymentRequestEligibilityCheckerInterface $updatePaymentRequestEligibilityChecker,
        PaymentRequestInterface $paymentRequest
    ): void {
        $hash = 'hash123!@#';
        $paymentRequestRepository->find($hash)->willReturn($paymentRequest);
        $updatePaymentRequestEligibilityChecker->isEligible($paymentRequest)->willReturn(false);

        $this
            ->getItem(PaymentRequestInterface::class, $hash, 'shop_get')
            ->shouldReturn(null);
    }

    function it_providers_payment_request_when_update_payment_request_is_eligible(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        UpdatePaymentRequestEligibilityCheckerInterface $updatePaymentRequestEligibilityChecker,
        PaymentRequestInterface $paymentRequest
    ): void {
        $hash = 'hash123!@#';
        $paymentRequestRepository->find($hash)->willReturn($paymentRequest);
        $updatePaymentRequestEligibilityChecker->isEligible($paymentRequest)->willReturn(true);

        $this
            ->getItem(PaymentRequestInterface::class, $hash, 'shop_get')
            ->shouldReturn($paymentRequest);
    }
}
