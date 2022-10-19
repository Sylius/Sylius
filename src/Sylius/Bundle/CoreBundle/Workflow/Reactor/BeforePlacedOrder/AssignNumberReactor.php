<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Reactor\BeforePlacedOrder;

use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class AssignNumberReactor implements BeforePlacedOrderReactorInterface
{
    public function __construct(private OrderNumberAssignerInterface $orderNumberAssigner)
    {
    }

    public function react(OrderInterface $order): void
    {
        $this->orderNumberAssigner->assignNumber($order);
    }
}
