<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderCheckoutStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;

abstract class AbstractCheckoutStateResolverCallback implements AfterCompletedCheckoutCallbackInterface
{
    public function __construct(private OrderCheckoutStateResolverInterface $orderCheckoutStateResolver)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->orderCheckoutStateResolver->resolve($order);
    }
}
