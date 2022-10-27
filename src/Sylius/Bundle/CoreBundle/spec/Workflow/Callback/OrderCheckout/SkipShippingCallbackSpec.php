<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterAddressedCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\SkipShippingCallback;
use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderCheckoutStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class SkipShippingCallbackSpec extends ObjectBehavior
{
    function let(OrderCheckoutStateResolverInterface $checkoutStateResolver): void
    {
        $this->beConstructedWith($checkoutStateResolver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SkipShippingCallback::class);
    }

    function it_is_called_after_addressed(): void
    {
        $this->shouldImplement(AfterAddressedCallbackInterface::class);
    }

    function it_skips_payment(
        OrderInterface $order,
        OrderCheckoutStateResolverInterface $checkoutStateResolver,
    ): void {
        $checkoutStateResolver->resolve($order)->shouldBeCalled();

        $this->call($order);
    }
}
