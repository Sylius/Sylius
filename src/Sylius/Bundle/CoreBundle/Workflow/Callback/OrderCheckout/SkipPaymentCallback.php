<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderCheckoutStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class SkipPaymentCallback implements AfterSelectedShippingCallbackInterface
{
    public function __construct(private OrderCheckoutStateResolverInterface $checkoutStateResolver)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->checkoutStateResolver->resolve($order);
    }
}
