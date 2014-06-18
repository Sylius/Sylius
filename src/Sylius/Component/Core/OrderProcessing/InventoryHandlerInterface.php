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
use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * Order inventory handler service interface.
 *
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
     * Processes inventory units.
     *
     * @param OrderInterface $order
     */
    public function processInventoryUnits(OrderItemInterface $order);

    /**
     * Put inventory on hold.
     *
     * @param OrderInterface $order
     */
    public function holdInventory(OrderInterface $order);

    /**
     * Release inventory.
     *
     * @param OrderInterface $order
     */
    public function releaseInventory(OrderInterface $order);

    /**
     * Update the inventory state accordingly.
     *
     * @param OrderInterface $order
     */
    public function updateInventory(OrderInterface $order);
}
