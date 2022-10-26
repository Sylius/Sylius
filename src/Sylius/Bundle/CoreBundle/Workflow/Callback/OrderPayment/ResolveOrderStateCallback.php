<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment;

use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderStateResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ResolveOrderStateCallback implements AfterPaidCallbackInterface
{
    public function __construct(private OrderStateResolverInterface $orderStateResolver)
    {
    }

    public function call(OrderInterface $order): void
    {
       $this->orderStateResolver->resolve($order);
    }
}
