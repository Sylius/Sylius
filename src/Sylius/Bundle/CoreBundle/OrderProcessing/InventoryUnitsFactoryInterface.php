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
 * Order inventory units factory service interface.
 *
 * Service implementing this interface, should be able to create
 * all the inventory units for the order.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface InventoryUnitsFactoryInterface
{
    /**
     * Creates order inventory units.
     *
     * @param OrderInterface $order
     */
    public function createInventoryUnits(OrderInterface $order);
}
