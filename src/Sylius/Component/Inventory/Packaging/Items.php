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

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Inventory\Model\InventorySubjectInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Items to package overview.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Items
{
    private $subject;
    private $inventoryUnits;

    private $stockables = array();
    private $items = array();
    private $total = 0;

    private $overall = array();
    private $remaining = array();

    public function __construct(InventorySubjectInterface $subject)
    {
        $this->subject = $subject;
        $this->inventoryUnits = $subject->getInventoryUnits();
        $this->stockables = new ArrayCollection();

        $this->total = count($this->inventoryUnits);

        foreach ($this->inventoryUnits as $unit) {
            if (!$unit instanceof InventoryUnitInterface) {
                throw new \InvalidArgumentException(sprintf('Expected instance of "Sylius\Component\Inventory\Model\InventoryUnitInterface", "%s" given.', is_object($splitter) ? get_class($splitter) : gettype($splitter)));
            }

            $stockable = $unit->getStockable();
            $id = spl_object_hash($stockable);

            if (!$this->stockables->contains($stockable)) {
                $this->stockables->add($stockable);
            }

            $this->items[$id]['units'][] = $unit;
        }

        foreach ($this->items as $id => $data) {
            $this->overall[$id] = count($data['units']);
            $this->remaining[$id] = count($data['units']);
        }
    }

    public function getStockables()
    {
        return $this->stockables;
    }

    public function getRemaining(StockableInterface $stockable)
    {
        return $this->remaining[spl_object_hash($stockable)];
    }

    public function getInventoryUnitForPacking(StockableInterface $stockable)
    {
        if (0 === $this->getRemaining($stockable)) {
            return null;
        }

        $id = spl_object_hash($stockable);
        $this->remaining[$id]--;

        return array_pop($this->items[$id]['units']);
    }
}
