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
use Sylius\Component\Order\Model\AdjustmentInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnit implements InventoryUnitInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * @var string
     */
    protected $inventoryState = InventoryUnitInterface::STATE_CHECKOUT;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var integer
     */
    protected $adjustmentsTotal;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->adjustments = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockable()
    {
        return $this->stockable;
    }

    /**
     * {@inheritdoc}
     */
    public function setStockable(StockableInterface $stockable)
    {
        $this->stockable = $stockable;
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->stockable->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return $this->stockable->getInventoryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryState()
    {
        return $this->inventoryState;
    }

    /**
     * {@inheritdoc}
     */
    public function setInventoryState($state)
    {
        $this->inventoryState = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function isSold()
    {
        return InventoryUnitInterface::STATE_SOLD === $this->inventoryState;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackordered()
    {
        return InventoryUnitInterface::STATE_BACKORDERED === $this->inventoryState;
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

    /**
     * {@inheritDoc}
     */
    public function getAdjustments($type = null)
    {
        if (null == $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function addAdjustment(AdjustmentInterface $adjustment)
    {
        if ($this->adjustments->contains($adjustment)) {
            return;
        }

        $adjustment->setAdjustable($this);
        $this->adjustments->add($adjustment);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$this->adjustments->contains($adjustment)) {
            return;
        }

        $this->adjustments->removeElement($adjustment);
    }

    /**
     * {@inheritDoc}
     */
    public function getAdjustmentsTotal($type = null)
    {
        $amount = 0;

        foreach ($this->adjustments as $adjustment) {
            if ($type && $type !== $adjustment->getType()) {
                continue;
            }

            $amount += $adjustment->getAmount();
        }

        $this->adjustmentsTotal = $amount;

        return $this->adjustmentsTotal;
    }

    /**
     * {@inheritDoc}
     */
    public function removeAdjustments($type)
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            if ($type === $adjustment->getType() && !$adjustment->isLocked()) {
                $this->removeAdjustment($adjustment);
            }
        }
    }

    public function clearAdjustments()
    {
        $this->adjustments->clear();
    }
}
