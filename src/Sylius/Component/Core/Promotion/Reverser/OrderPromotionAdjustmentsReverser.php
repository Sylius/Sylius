<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Reverser;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class OrderPromotionAdjustmentsReverser implements OrderPromotionAdjustmentsReverserInterface
{
    /**
     * {@inheritdoc}
     */
    public function revert(OrderInterface $order, PromotionInterface $promotion)
    {
        foreach ($order->getItems() as $item) {
            $this->revertItem($promotion, $item);
        }
    }

    /**
     * @param PromotionInterface $promotion
     * @param OrderItemInterface $item
     */
    private function revertItem(PromotionInterface $promotion, OrderItemInterface $item)
    {
        foreach ($item->getUnits() as $unit) {
            $this->revertUnit($promotion, $unit);
        }
    }

    /**
     * @param PromotionInterface $promotion
     * @param OrderItemUnitInterface $unit
     */
    private function revertUnit(PromotionInterface $promotion, OrderItemUnitInterface $unit)
    {
        foreach ($unit->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $unit->removeAdjustment($adjustment);
            }
        }
    }
}
