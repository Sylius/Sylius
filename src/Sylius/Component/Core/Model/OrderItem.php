<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Cart\Model\CartItem;
use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;
use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderItem extends CartItem implements OrderItemInterface
{
    /**
     * @var ProductVariantInterface
     */
    protected $variant;

    /**
     * @var Collection|InventoryUnitInterface[]
     */
    protected $inventoryUnits;

    /**
     * @var Collection|BasePromotionInterface[]
     */
    protected $promotions;

    /**
     * @var Collection|OrderItemUnitInterface[]
     */
    protected $itemUnits;

    public function __construct()
    {
        parent::__construct();

        $this->inventoryUnits = new ArrayCollection();
        $this->promotions = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->variant->getProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(ProductVariantInterface $variant)
    {
        $this->variant = $variant;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(BaseOrderItemInterface $item)
    {
        return parent::equals($item) || ($item instanceof self
            && $item->getVariant() === $this->variant
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryUnits()
    {
        return $this->inventoryUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function addInventoryUnit(InventoryUnitInterface $unit)
    {
        if (!$this->hasInventoryUnit($unit)) {
            $unit->setOrderItem($this);
            $this->inventoryUnits->add($unit);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeInventoryUnit(InventoryUnitInterface $unit)
    {
        $unit->setOrderItem(null);
        $this->inventoryUnits->removeElement($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function hasInventoryUnit(InventoryUnitInterface $unit)
    {
        return $this->inventoryUnits->contains($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectTotal()
    {
        return $this->getTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectCount()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotion(BasePromotionInterface $promotion)
    {
        return $this->promotions->contains($promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function addPromotion(BasePromotionInterface $promotion)
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotion(BasePromotionInterface $promotion)
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemUnits()
    {
        return $this->itemUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function addItemUnit(OrderItemUnitInterface $itemUnit)
    {
        if (!$this->hasItemUnit($itemUnit)) {
            $itemUnit->setOrderItem($this);
            $this->itemUnits->add($itemUnit);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeItemUnit(OrderItemUnitInterface $itemUnit)
    {
        $itemUnit->setOrderItem(null);
        $this->itemUnits->removeElement($itemUnit);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemUnit(OrderItemUnitInterface $itemUnit)
    {
        return $this->itemUnits->contains($itemUnit);
    }
}
