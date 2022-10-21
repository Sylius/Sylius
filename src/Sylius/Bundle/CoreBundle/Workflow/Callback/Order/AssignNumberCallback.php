<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class AssignNumberCallback implements BeforePlacedOrderCallbackInterface
{
    public function __construct(private OrderNumberAssignerInterface $orderNumberAssigner)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->orderNumberAssigner->assignNumber($order);
    }
}
