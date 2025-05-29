<?php

declare(strict_types=1);

namespace App\Service;

use Sylius\Component\Core\Model\OrderItemInterface;

interface OrderItemReordererInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function reorder(OrderItemInterface $orderItem): void;

}