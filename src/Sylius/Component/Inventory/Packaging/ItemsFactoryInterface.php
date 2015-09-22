<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Packaging;

use Sylius\Component\Inventory\Model\InventorySubjectInterface;

/**
 * This service builds an inventory overview based on subject inventory units.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ItemsFactoryInterface
{
    /**
     * Get items overview.
     *
     * @param InventorySubjectInterface
     *
     * @return Items
     */
    public function createItems(InventorySubjectInterface $subject);
}
