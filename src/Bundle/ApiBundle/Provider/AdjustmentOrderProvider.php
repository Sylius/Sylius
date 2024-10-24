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

namespace Sylius\Bundle\ApiBundle\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Model\OrderInterface;

/** @experimental */
final class AdjustmentOrderProvider implements AdjustmentOrderProviderInterface
{
    public function provide(AdjustmentInterface $adjustment): ?OrderInterface
    {
        switch ($adjustment) {
            case $adjustment->getAdjustable() instanceof OrderInterface:
                return $adjustment->getOrder();
            case $adjustment->getAdjustable() instanceof OrderItemInterface:
                return $adjustment->getOrderItem()->getOrder();
            case $adjustment->getAdjustable() instanceof OrderItemUnitInterface:
                return $adjustment->getOrderItemUnit()->getOrderItem()->getOrder();
            default:
                return null;
        }
    }
}
