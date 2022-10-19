<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Processor\Order;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderProcessorInterface
{
    public function process(OrderInterface $order): void;
}
