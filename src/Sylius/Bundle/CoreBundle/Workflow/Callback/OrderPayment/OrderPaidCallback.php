<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment;

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderPaidCallback implements AfterPaidCallbackInterface
{
    public function __construct(private OrderInventoryOperatorInterface $inventoryOperator)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->inventoryOperator->sell($order);
    }
}
