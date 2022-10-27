<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Payment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterAuthorizedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterCompletedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterProcessedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterRefundedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\ResolveOrderPaymentStateCallback;
use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderPaymentStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ResolveOrderPaymentStateCallbackSpec extends ObjectBehavior
{
    function let(OrderPaymentStateResolverInterface $orderPaymentStateResolver): void
    {
        $this->beConstructedWith($orderPaymentStateResolver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResolveOrderPaymentStateCallback::class);
    }

    function it_is_called_after_completed_payment(): void
    {
        $this->shouldImplement(AfterCompletedPaymentCallbackInterface::class);
    }

    function it_is_called_after_processed_payment(): void
    {
        $this->shouldImplement(AfterProcessedPaymentCallbackInterface::class);
    }

    function it_is_called_after_refunded_payment(): void
    {
        $this->shouldImplement(AfterRefundedPaymentCallbackInterface::class);
    }

    function it_is_called_after_authorized_payment(): void
    {
        $this->shouldImplement(AfterAuthorizedPaymentCallbackInterface::class);
    }

    function it_resolves_order_payment_state(
        PaymentInterface $payment,
        OrderInterface $order,
        OrderPaymentStateResolverInterface $orderPaymentStateResolver,
    ): void {
        $payment->getOrder()->willReturn($order);

        $orderPaymentStateResolver->resolve($order)->shouldBeCalled();

        $this->call($payment);
    }
}
