<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

final class DecrementPromotionsUsagesCallback implements AfterCanceledOrderCallbackInterface
{
    public function __construct(private OrderPromotionsUsageModifierInterface $promotionsUsageModifier)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->promotionsUsageModifier->decrement($order);
    }
}
