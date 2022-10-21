<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\StateResolver;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderCheckoutStateResolverInterface
{
    public function resolve(OrderInterface $order): void;
}
