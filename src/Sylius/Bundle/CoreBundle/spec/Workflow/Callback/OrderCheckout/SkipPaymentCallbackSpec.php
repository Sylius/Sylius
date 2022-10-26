<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\SkipPaymentCallback;
use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderCheckoutStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class SkipPaymentCallbackSpec extends ObjectBehavior
{
    function let(OrderCheckoutStateResolverInterface $checkoutStateResolver): void
    {
        $this->beConstructedWith($checkoutStateResolver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SkipPaymentCallback::class);
    }

    function it_skips_payment(
        OrderInterface $order,
        OrderCheckoutStateResolverInterface $checkoutStateResolver,
    ): void {
        $checkoutStateResolver->resolve($order)->shouldBeCalled();

        $this->call($order);
    }
}
