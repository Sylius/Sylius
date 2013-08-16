<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\OrderProcessing;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;

/**
 * Order inventory handler service interface.
 *
 * Service implementing this interface, should be able to handle
 * all the inventory units for the order.
 *
 * It also updates inventory after order is complete.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface InventoryHandlerInterface
{
    /**
     * Processes inventory units.
     *
     * @param OrderInterface $order
     */
    public function processInventoryUnits(OrderInterface $order);

    /**
     * Update the inventory state accordingly.
     *
     * @param OrderInterface $order
     */
    public function updateInventory(OrderInterface $order);
}
