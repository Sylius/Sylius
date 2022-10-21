<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class HoldInventoryCallback implements AfterPlacedOrderCallbackInterface
{
    public function __construct(private OrderInventoryOperatorInterface $inventoryOperator)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->inventoryOperator->hold($order);
    }
}
