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
use Sylius\Component\Customization\Model\CustomizationValueInterface;

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
     * @var Collection|BasePromotionInterface[]
     */
    protected $promotions;

    /*
     * Customization values.
     *
     * @var Collection|CustomizationValueInterface[]
     */
    protected $customizationValues;

    public function __construct()
    {
        parent::__construct();

        $this->inventoryUnits = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->customizationValues = new ArrayCollection;
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
            && $item->getCustomizationSubject()->getCustomizations()->isEmpty()
            && $this->getCustomizationSubject()->getCustomizations()->isEmpty()
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotion(BasePromotionInterface $promotion)
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

    /**
     * {@inheritdoc}
     */
    public function getCustomizationValues()
    {
        return $this->customizationValues;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizationValues(ArrayCollection $customizationValues)
    {
        foreach ($customizationValues as $customizationValue) {
            $customizationValue->setSubjectInstance($this);
        }

        $this->customizationValues = $customizationValues;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomizationValue(CustomizationValueInterface $customizationValue)
    {
        if (!$this->hasCustomizationValue($customizationValue)) {
            $customizationValue->setSubjectInstance($this);
            $this->customizationValues->add($customizationValue);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCustomizationValue(CustomizationValueInterface $customizationValue)
    {
        if ($this->hasCustomizationValue($customizationValue)) {
            $customizationValue->setSubjectInstance(null);
            $this->customizationValues->removeElement($customizationValue);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomizationValue(CustomizationValueInterface $customizationValue)
    {
        return $this->customizationValues->contains($customizationValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizationValueByName($name)
    {
        foreach ($this->customizationValues as $customizationValue) {
            if ($name === $customizationValue->getCustomization()->getName()) {
                return $customizationValue;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizationSubject()
    {
        return $this->getProduct();
    }
}
