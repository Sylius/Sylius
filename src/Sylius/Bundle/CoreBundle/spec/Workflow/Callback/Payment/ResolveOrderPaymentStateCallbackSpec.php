<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Payment;

use PhpSpec\ObjectBehavior;
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
