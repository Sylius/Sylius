<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Reactor;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderReactorInterface
{
    public function react(OrderInterface $order);
}
