<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Processor\Order\BeforeCreate;

use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class AssignNumberProcessor implements BeforeOrderCreateProcessorInterface
{
    public function __construct(private OrderNumberAssignerInterface $orderNumberAssigner)
    {
    }

    public function process(OrderInterface $order): void
    {
        $this->orderNumberAssigner->assignNumber($order);
    }
}
