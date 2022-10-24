<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use Sylius\Component\Core\Model\OrderInterface;

interface AfterSelectedShippingCallbackInterface
{
    public function call(OrderInterface $order): void;
}
