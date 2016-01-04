<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Stock items aware interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockItemsAwareInterface
{
    /**
     * @return Collection|StockItemInterface[]
     */
    public function getStockItems();
}
