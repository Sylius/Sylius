<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Reactor\BeforePlacedOrder;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;

final class AssignTokenReactor implements BeforePlacedOrderReactorInterface
{
    public function __construct(private OrderTokenAssignerInterface $orderTokenAssigner)
    {
    }

    public function react(OrderInterface $order): void
    {
        $this->orderTokenAssigner->assignTokenValue($order);
    }
}
