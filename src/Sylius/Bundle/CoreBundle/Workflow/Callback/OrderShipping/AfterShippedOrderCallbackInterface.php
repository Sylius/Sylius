<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping;

use Sylius\Component\Core\Model\OrderInterface;

interface AfterShippedOrderCallbackInterface
{
    public function call(OrderInterface $order): void;
}
