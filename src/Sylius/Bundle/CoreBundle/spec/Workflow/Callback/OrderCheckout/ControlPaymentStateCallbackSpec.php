<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\ControlPaymentStateCallback;
use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderCheckoutStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ControlPaymentStateCallbackSpec extends ObjectBehavior
{
    function let(OrderCheckoutStateResolverInterface $orderCheckoutStateResolver): void
    {
        $this->beConstructedWith($orderCheckoutStateResolver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ControlPaymentStateCallback::class);
    }

    function it_resolves_order_checkout_state(
        OrderInterface $order,
        OrderCheckoutStateResolverInterface $orderCheckoutStateResolver,
    ): void {
        $orderCheckoutStateResolver->resolve($order)->shouldBeCalled();

        $this->call($order);
    }
}
