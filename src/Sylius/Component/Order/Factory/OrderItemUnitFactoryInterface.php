<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface OrderItemUnitFactoryInterface extends FactoryInterface
{
    /**
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemUnitInterface
     */
    public function createForItem(OrderItemInterface $orderItem): OrderItemUnitInterface;
}
