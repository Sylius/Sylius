<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping;

use Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment\AfterShippedCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderShippingStateResolverInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class ResolveOrderShippingStateCallback implements AfterShippedCallbackInterface
{
    public function __construct(private OrderShippingStateResolverInterface $orderShippingStateResolver)
    {
    }

    public function call(ShipmentInterface $shipment): void
    {
       $this->orderShippingStateResolver->resolve($shipment->getOrder());
    }
}
