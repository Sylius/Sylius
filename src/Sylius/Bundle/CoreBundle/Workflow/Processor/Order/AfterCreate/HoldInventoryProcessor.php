<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterCreate;

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class HoldInventoryProcessor implements AfterOrderCreateProcessorInterface
{
    public function __construct(private OrderInventoryOperatorInterface $inventoryOperator)
    {
    }

    public function process(OrderInterface $order): void
    {
        $this->inventoryOperator->hold($order);
    }
}
