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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Stock location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocation implements StockLocationInterface
{
    /**
     * Stock item id.
     *
     * @var mixed
     */
    protected $id;

    protected $name;
    protected $code;
    protected $enabled = true;

    /**
     * Shipments.
     *
     * @var ShipmentInterface[]
     */
    protected $shipments;

    /**
     * Stock items.
     *
     * @var StockItemInterface[]
     */
    protected $stockItems;

    /**
     * Stock movements.
     *
     * @var StockMovementInterface[]
     */
    protected $stockMovements;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->shipments = new ArrayCollection();
        $this->stockItems = new ArrayCollection();
        $this->stockMovements = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->name, $this->code);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = (Boolean) $enabled;

        return $this;
    }

    public function getStockItems()
    {
        return $this->stockItems;
    }

    public function addStockItem(StockItemInterface $item)
    {
        if (!$this->hasStockItem($item)) {
            $this->stockItems->add($item);
        }

        return $this;
    }

    public function removeStockItem(StockItemInterface $item)
    {
        if ($this->hasStockItem($item)) {
            $this->stockItems->removeElement($item);
        }

        return $this;
    }

    public function hasStockItem(StockItemInterface $item)
    {
        return $this->stockItems->contains($item);
    }

    public function getStockMovements()
    {
        return $this->stockMovements;
    }

    public function addStockMovement(StockMovementInterface $movement)
    {
        if (!$this->hasStockMovement($movement)) {
            $this->stockMovements->add($movement);
        }

        return $this;
    }

    public function removeStockMovement(StockMovementInterface $movement)
    {
        if ($this->hasStockMovement($movement)) {
            $this->stockMovements->removeElement($movement);
        }

        return $this;
    }

    public function hasStockMovement(StockMovementInterface $movement)
    {
        return $this->stockMovements->contains($movement);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createAt)
    {
        $this->createdAt = $createAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
