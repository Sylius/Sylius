<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Inventory\Model;

class InventoryUnit implements InventoryUnitInterface
{
    /** @var mixed */
    protected $id;

    /** @var StockableInterface|null */
    protected $stockable;

    public function getId()
    {
        return $this->id;
    }

    public function getStockable(): ?StockableInterface
    {
        return $this->stockable;
    }

    public function setStockable(StockableInterface $stockable): void
    {
        $this->stockable = $stockable;
    }
}
