<?php

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Calculator;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderItemsSubtotalCalculatorInterface
{
    public function getSubtotal(OrderInterface $order): int;
}
