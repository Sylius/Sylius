<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Remover;

use Sylius\Core\Model\AdjustmentInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\OrderItemUnitInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AdjustmentsRemover implements AdjustmentsRemoverInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeFrom(OrderInterface $order)
    {
        $adjustmentsToRemove = [
            AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT,
            AdjustmentInterface::TAX_ADJUSTMENT
        ];

        foreach ($adjustmentsToRemove as $type) {
            $order->removeAdjustmentsRecursively($type);
        }
    }
}
