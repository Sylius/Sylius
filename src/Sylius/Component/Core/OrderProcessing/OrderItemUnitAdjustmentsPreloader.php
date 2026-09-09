<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderItemUnitAdjustmentsPreloader implements OrderProcessorInterface
{
    public function __construct(private OrderItemUnitRepositoryInterface $orderItemUnitRepository)
    {
    }

    public function process(OrderInterface $order): void
    {
        if (!$order->canBeProcessed()) {
            return;
        }

        $units = [];
        foreach ($order->getItems() as $item) {
            foreach ($item->getUnits() as $unit) {
                if (null === $unit->getId()) {
                    continue;
                }

                $units[] = $unit;
            }
        }

        $this->orderItemUnitRepository->preloadAdjustments($units);
    }
}
