<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Daniel Gorgan <danut007ro@gmail.com>
 */
interface OrderItemFactoryInterface extends FactoryInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return OrderItemInterface
     */
    public function createForOrder(OrderInterface $order);
}
