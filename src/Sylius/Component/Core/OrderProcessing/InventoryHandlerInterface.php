<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Service implementing this interface, should be able to handle
 * all the inventory units for the order.
 *
 * It also updates inventory after order is complete.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface InventoryHandlerInterface
{
    /**
     * @param OrderInterface $order
     */
    public function holdInventory(OrderInterface $order);

    /**
     * @param OrderInterface $order
     */
    public function releaseInventory(OrderInterface $order);

    /**
     * @param OrderInterface $order
     */
    public function updateInventory(OrderInterface $order);
}
