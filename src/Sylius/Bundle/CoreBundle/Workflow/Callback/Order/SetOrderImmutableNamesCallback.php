<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Order\OrderItemNamesSetterInterface;

final class SetOrderImmutableNamesCallback implements AfterPlacedOrderCallbackInterface
{
    public function __construct(private OrderItemNamesSetterInterface $orderItemNamesSetter)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->orderItemNamesSetter->__invoke($order);
    }
}
