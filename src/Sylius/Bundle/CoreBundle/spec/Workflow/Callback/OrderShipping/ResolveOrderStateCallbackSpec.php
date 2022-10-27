<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping\AfterShippedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping\ResolveOrderStateCallback;
use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ResolveOrderStateCallbackSpec extends ObjectBehavior
{
    function let(OrderStateResolverInterface $orderStateResolver): void
    {
        $this->beConstructedWith($orderStateResolver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResolveOrderStateCallback::class);
    }

    function it_is_called_after_shipped_order(): void
    {
        $this->shouldImplement(AfterShippedOrderCallbackInterface::class);
    }

    function it_resolves_order_state(
        OrderInterface $order,
        OrderStateResolverInterface $orderStateResolver,
    ): void {
        $orderStateResolver->resolve($order)->shouldBeCalled();

        $this->call($order);
    }
}
