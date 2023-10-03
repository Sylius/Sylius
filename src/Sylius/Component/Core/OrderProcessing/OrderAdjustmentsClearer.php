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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderAdjustmentsClearer implements OrderProcessorInterface
{
    private array $adjustmentsToRemove;

    public function __construct(array $adjustmentsToRemove = [])
    {
        if (0 === func_num_args()) {
            trigger_deprecation(
                'sylius/core',
                '1.2',
                'Not passing $adjustmentsToRemove explicitly is deprecated and will be prohibited in Sylius 2.0',
            );

            $adjustmentsToRemove = [
                AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT,
                AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
                AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT,
                AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT,
                AdjustmentInterface::TAX_ADJUSTMENT,
            ];
        }

        $this->adjustmentsToRemove = $adjustmentsToRemove;
    }

    public function process(OrderInterface $order): void
    {
        if (!$order->canBeProcessed()) {
            return;
        }

        foreach ($this->adjustmentsToRemove as $type) {
            $order->removeAdjustmentsRecursively($type);
        }
    }
}
