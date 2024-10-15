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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\PaymentRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProviderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class ItemProviderSpec extends ObjectBehavior
{
    public function let(
        SectionProviderInterface $sectionProvider,
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
    ): void {
        $this->beConstructedWith($sectionProvider, $paymentRequestRepository, $finalizedPaymentRequestChecker);
    }

    function it_is_a_state_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    function it_throws_an_exception_if_operation_class_is_not_payment(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);
        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_if_operation_is_not_put(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(PaymentRequestInterface::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_if_section_is_not_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Put(class: PaymentRequestInterface::class, name: 'put');
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation, [], []]);
    }

    function it_returns_nothing_if_payment_request_is_not_found(
        SectionProviderInterface $sectionProvider,
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
    ): void {
        $hash = 'hash';
        $operation = new Put(class: PaymentRequestInterface::class, name: 'put');
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $paymentRequestRepository->find($hash)->willReturn(null);
        $finalizedPaymentRequestChecker->isFinal(Argument::any())->shouldNotBeCalled();

        $this->provide($operation, ['hash' => $hash], [])->shouldReturn(null);
    }

    function it_returns_nothing_if_payment_request_is_in_final_state(
        SectionProviderInterface $sectionProvider,
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
        PaymentRequestInterface $paymentRequest,
    ): void {
        $hash = 'hash';
        $operation = new Put(class: PaymentRequestInterface::class, name: 'put');
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $paymentRequestRepository->find($hash)->willReturn($paymentRequest);
        $finalizedPaymentRequestChecker->isFinal($paymentRequest)->willReturn(true);

        $this->provide($operation, ['hash' => $hash], [])->shouldReturn(null);
    }

    function it_returns_payment_request_by_hash(
        SectionProviderInterface $sectionProvider,
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
        PaymentRequestInterface $paymentRequest,
    ): void {
        $hash = 'hash';
        $operation = new Put(class: PaymentRequestInterface::class, name: 'put');
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $paymentRequestRepository->find($hash)->willReturn($paymentRequest);
        $finalizedPaymentRequestChecker->isFinal($paymentRequest)->willReturn(false);

        $this->provide($operation, ['hash' => $hash], [])->shouldReturn($paymentRequest);
    }
}
