<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\StateResolver\PaymentTargetTransitionResolver;
use Sylius\Component\Order\StateResolver\TargetTransitionResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class PaymentTargetTransitionResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentTargetTransitionResolver::class);
    }

    function it_implements_an_order_state_resolver_interface()
    {
        $this->shouldImplement(TargetTransitionResolverInterface::class);
    }

    function it_resolves_refunded_if_all_its_payments_are_refunded(
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $this->resolve($order)->shouldReturn(OrderPaymentTransitions::TRANSITION_REFUND);
    }

    function it_resolves_refunded_if_its_payments_are_refunded_or_failed_but_at_least_one_is_refunded(
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(10000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $secondPayment->getAmount()->willReturn(10000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;

        $order->getTotal()->willReturn(10000);

        $this->resolve($order)->shouldReturn(OrderPaymentTransitions::TRANSITION_REFUND);
    }

    function it_resolves_paid_if_fully_paid(
        OrderInterface $order,
        PaymentInterface $payment
    ) {
        $payment->getAmount()->willReturn(10000);
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payments = new ArrayCollection([$payment->getWrappedObject()]);

        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(10000);

        $this->resolve($order)->shouldReturn(OrderPaymentTransitions::TRANSITION_PAY);
    }

    function it_resolves_paid_if_fully_paid_even_if_previous_payment_was_failed(
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(10000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $secondPayment->getAmount()->willReturn(10000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payments = new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]);

        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(10000);

        $this->resolve($order)->shouldReturn(OrderPaymentTransitions::TRANSITION_PAY);
    }

    function it_resolves_partially_refunded_if_one_of_the_payment_is_completed(
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $this->resolve($order)->shouldReturn(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND);
    }

    function it_resolves_completed_if_fully_paid_multiple_payments(
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $this->resolve($order)->shouldReturn(OrderPaymentTransitions::TRANSITION_PAY);
    }

    function it_resolves_partially_paid_if_one_of_the_payment_is_processing(
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $this->resolve($order)->shouldReturn(OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY);
    }
}
