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

namespace spec\Sylius\Component\Core\Payment\Remover;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

final class OrderPaymentsRemoverSpec extends ObjectBehavior
{
    function it_implements_order_payments_remover_interface(): void
    {
        $this->shouldImplement(OrderPaymentsRemoverInterface::class);
    }

    function it_should_remove_payments_of_a_free_orders(OrderInterface $order): void
    {
        $order->getTotal()->willReturn(0);

        $this->canRemovePayments($order)->shouldReturn(true);
    }

    function it_should_not_remove_payments_of_a_not_free_orders(OrderInterface $order): void
    {
        $order->getTotal()->willReturn(1);

        $this->canRemovePayments($order)->shouldReturn(false);
    }

    function it_does_nothing_when_order_has_no_payments(OrderInterface $order): void
    {
        $order->getPayments()->willReturn(new ArrayCollection());

        $order->removePayment(Argument::any())->shouldNotBeCalled();

        $this->removePayments($order);
    }

    function it_removes_only_payments_with_state_cart(
        OrderInterface $order,
        PaymentInterface $cartPayment,
        PaymentInterface $authorizedPayment,
        PaymentInterface $newPayment,
        PaymentInterface $processingPayment,
        PaymentInterface $completedPayment,
        PaymentInterface $failedPayment,
        PaymentInterface $cancelledPayment,
        PaymentInterface $refundedPayment,
        PaymentInterface $unknownPayment,
    ): void {
        $cartPayment->getState()->willReturn(PaymentInterface::STATE_CART);
        $authorizedPayment->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);
        $newPayment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $processingPayment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $completedPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $failedPayment->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $cancelledPayment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $refundedPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);
        $unknownPayment->getState()->willReturn(PaymentInterface::STATE_UNKNOWN);

        $order->getPayments()->willReturn(new ArrayCollection([
            $cartPayment->getWrappedObject(),
            $authorizedPayment->getWrappedObject(),
            $newPayment->getWrappedObject(),
            $processingPayment->getWrappedObject(),
            $completedPayment->getWrappedObject(),
            $failedPayment->getWrappedObject(),
            $cancelledPayment->getWrappedObject(),
            $refundedPayment->getWrappedObject(),
            $unknownPayment->getWrappedObject(),
        ]));

        $order->removePayment($cartPayment)->shouldBeCalled();

        $order->removePayment($authorizedPayment)->shouldNotBeCalled();
        $order->removePayment($newPayment)->shouldNotBeCalled();
        $order->removePayment($processingPayment)->shouldNotBeCalled();
        $order->removePayment($completedPayment)->shouldNotBeCalled();
        $order->removePayment($failedPayment)->shouldNotBeCalled();
        $order->removePayment($cancelledPayment)->shouldNotBeCalled();
        $order->removePayment($refundedPayment)->shouldNotBeCalled();
        $order->removePayment($unknownPayment)->shouldNotBeCalled();

        $this->removePayments($order);
    }
}
