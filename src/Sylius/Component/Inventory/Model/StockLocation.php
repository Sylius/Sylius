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
use Doctrine\Common\Collections\Collection;

/**
 * Stock location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocation implements StockLocationInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var Collection|StockItemInterface[]
     */
    protected $stockItems;

    /**
     * @var Collection|StockMovementInterface[]
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
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    /**
     * {@inheritdoc}
     */
    public function addStockItem(StockItemInterface $item)
    {
        if (!$this->hasStockItem($item)) {
            $this->stockItems->add($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStockItem(StockItemInterface $item)
    {
        if ($this->hasStockItem($item)) {
            $this->stockItems->removeElement($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasStockItem(StockItemInterface $item)
    {
        return $this->stockItems->contains($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getStockMovements()
    {
        return $this->stockMovements;
    }

    /**
     * {@inheritdoc}
     */
    public function addStockMovement(StockMovementInterface $movement)
    {
        if (!$this->hasStockMovement($movement)) {
            $this->stockMovements->add($movement);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStockMovement(StockMovementInterface $movement)
    {
        if ($this->hasStockMovement($movement)) {
            $this->stockMovements->removeElement($movement);
        }
    }

    /**
     * {@inheritdoc}
     */
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
    }
}
