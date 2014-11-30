<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

/**
 * Subject which is a concern of inventory tracking.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface InventorySubjectInterface
{
    public function getInventoryUnits();
}
