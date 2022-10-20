<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\BeforePlacedOrder;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;

final class AssignTokenCallback implements BeforePlacedOrderCallbackInterface
{
    public function __construct(private OrderTokenAssignerInterface $orderTokenAssigner)
    {
    }

    public function run(OrderInterface $order): void
    {
        $this->orderTokenAssigner->assignTokenValue($order);
    }
}
