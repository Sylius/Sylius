<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment\ResolveOrderShippingStateCallback;
use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderShippingStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class ResolveOrderShippingStateCallbackSpec extends ObjectBehavior
{
    function let(OrderShippingStateResolverInterface $orderShippingStateResolver): void
    {
        $this->beConstructedWith($orderShippingStateResolver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResolveOrderShippingStateCallback::class);
    }

    function it_resolves_order_shipping_state(
        ShipmentInterface $shipment,
        OrderInterface $order,
        OrderShippingStateResolverInterface $orderShippingStateResolver,
    ): void {
        $shipment->getOrder()->willReturn($order);

        $orderShippingStateResolver->resolve($order)->shouldBeCalled();

        $this->call($shipment);
    }
}
