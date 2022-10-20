<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\BeforePlacedOrder;

use Sylius\Component\Core\Model\OrderInterface;

interface BeforePlacedOrderCallbackInterface
{
    public function run(OrderInterface $order): void;
}
