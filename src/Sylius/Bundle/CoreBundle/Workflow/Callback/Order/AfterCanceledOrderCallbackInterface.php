<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Model\OrderInterface;

interface AfterCanceledOrderCallbackInterface
{
    public function call(OrderInterface $order): void;
}
