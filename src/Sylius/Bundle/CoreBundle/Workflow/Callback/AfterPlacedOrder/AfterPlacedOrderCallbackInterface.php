<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use Sylius\Component\Core\Model\OrderInterface;

interface AfterPlacedOrderCallbackInterface
{
    public function run(OrderInterface $order): void;
}
