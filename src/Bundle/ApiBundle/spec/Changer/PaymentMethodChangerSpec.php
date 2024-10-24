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

namespace spec\Sylius\Bundle\ApiBundle\Changer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class PaymentMethodChangerSpec extends ObjectBehavior
{
    function let(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
    ): void {
        $this->beConstructedWith($paymentRepository, $paymentMethodRepository);
    }

    function it_throws_an_exception_if_payment_method_with_given_code_has_not_been_found(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        OrderInterface $order,
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);

        $order->getId()->willReturn('100');

        $paymentRepository->findOneByOrderId('123', '100')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('changePaymentMethod', ['CASH_ON_DELIVERY_METHOD', '123', $order])
        ;
    }

    function it_throws_an_exception_if_payment_with_given_id_has_not_been_found(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        OrderInterface $order,
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $order->getId()->willReturn('444');

        $paymentRepository->findOneByOrderId('123', '444')->willReturn(null);

        $order->getState()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('changePaymentMethod', ['CASH_ON_DELIVERY_METHOD', '123', $order])
        ;
    }

    function it_throws_an_exception_if_payment_is_in_different_state_than_new(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        OrderInterface $order,
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $order->getId()->willReturn('444');

        $paymentRepository->findOneByOrderId('123', '444')->willReturn(null);

        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $payment->setMethod($paymentMethod)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('changePaymentMethod', ['CASH_ON_DELIVERY_METHOD', '123', $order])
        ;
    }

    function it_changes_payment_method_to_specified_payment(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        OrderInterface $order,
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $order->getId()->willReturn('444');

        $paymentRepository->findOneByOrderId('123', '444')->willReturn($payment);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $payment->setMethod($paymentMethod)->shouldBeCalled();

        $this->changePaymentMethod('CASH_ON_DELIVERY_METHOD', '123', $order)->shouldReturn($order);
    }
}
