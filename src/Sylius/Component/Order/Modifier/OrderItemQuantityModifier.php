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

namespace Sylius\Component\Order\Modifier;

use Sylius\Component\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

class OrderItemQuantityModifier implements OrderItemQuantityModifierInterface
{
    public function __construct(private OrderItemUnitFactoryInterface $orderItemUnitFactory)
    {
    }

    public function modify(OrderItemInterface $orderItem, int $targetQuantity): void
    {
        if ($orderItem->isSingleUnit()) {
            $this->modifySingleUnitItem($orderItem, $targetQuantity);

            return;
        }

        $currentQuantity = $orderItem->getQuantity();
        if (0 >= $targetQuantity || $currentQuantity === $targetQuantity) {
            return;
        }

        if ($targetQuantity < $currentQuantity) {
            $this->decreaseUnitsNumber($orderItem, $currentQuantity - $targetQuantity);
        } elseif ($targetQuantity > $currentQuantity) {
            $this->increaseUnitsNumber($orderItem, $targetQuantity - $currentQuantity);
        }
    }

    private function modifySingleUnitItem(OrderItemInterface $orderItem, int $targetQuantity): void
    {
        $this->decreaseUnitsNumber($orderItem, $targetQuantity);
        $this->orderItemUnitFactory->createSingleUnitForItem($orderItem, $targetQuantity);
    }

    private function increaseUnitsNumber(OrderItemInterface $orderItem, int $increaseBy): void
    {
        for ($i = 0; $i < $increaseBy; ++$i) {
            $this->orderItemUnitFactory->createForItem($orderItem);
        }
    }

    private function decreaseUnitsNumber(OrderItemInterface $orderItem, int $decreaseBy): void
    {
        foreach ($orderItem->getUnits() as $unit) {
            if (0 >= $decreaseBy--) {
                break;
            }

            $orderItem->removeUnit($unit);
        }
    }
}
