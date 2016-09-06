<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Inventory\Updater;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface OnHandQuantityUpdaterInterface
{
    /**
     * @var Collection|OrderItemInterface[]
     */
    public function decrease($orderItems);
}
