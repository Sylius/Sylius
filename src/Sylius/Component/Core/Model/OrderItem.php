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
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;

/**
 * Order item model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderItem extends CartItem implements OrderItemInterface
{
    /**
     * Product variant.
     *
     * @var ProductVariantInterface
     */
    protected $variant;

    /**
     * Inventory units.
     *
     * @var Collection|InventoryUnitInterface[]
     */
    protected $inventoryUnits;

    /**
     * Promotions applied
     *
     * @var Collection|PromotionInterface[]
     */
    protected $promotions;

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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(BaseOrderItemInterface $item)
    {
        return parent::equals($item) || ($item instanceof self
            && $item->getVariant() === $this->variant
            && $item->getUnitPrice() === $this->getUnitPrice()
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeInventoryUnit(InventoryUnitInterface $unit)
    {
        $unit->setOrderItem(null);
        $this->inventoryUnits->removeElement($unit);

        return $this;
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
    public function hasPromotion(PromotionInterface $promotion)
    {
        return $this->promotions->contains($promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function addPromotion(PromotionInterface $promotion)
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotion(PromotionInterface $promotion)
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotions()
    {
        return $this->promotions;
    }
}
