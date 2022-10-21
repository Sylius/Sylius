<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CancelOrderInventoryCallback implements AfterCanceledOrderCallbackInterface
{
    public function __construct(private OrderInventoryOperatorInterface $orderInventoryOperator)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->orderInventoryOperator->cancel($order);
    }
}
