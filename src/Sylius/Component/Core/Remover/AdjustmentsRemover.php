<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Remover;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AdjustmentsRemover implements AdjustmentsRemoverInterface
{
    /**
     * @var array
     */
    protected $adjustmentsToRemove = [
        AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
        AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
        AdjustmentInterface::TAX_ADJUSTMENT
    ];

    /**
     * {@inheritdoc}
     */
    public function remove(OrderInterface $order)
    {
        $units = $order->getItemUnits();
        foreach ($this->adjustmentsToRemove as $type) {
            $order->removeAdjustments($type);
            $this->removeUnitsAdjustments($units, $type);
        }
    }

    /**
     * @param \Traversable $units
     * @param string $type
     */
    private function removeUnitsAdjustments(\Traversable $units, $type)
    {
        /** @var OrderItemUnitInterface $unit */
        foreach ($units as $unit) {
            $unit->removeAdjustments($type);
        }
    }
}
