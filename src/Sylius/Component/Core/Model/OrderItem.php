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

    public function __construct()
    {
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
    public function calculateTotal()
    {
        $this->adjustmentsTotal = $this->calculateAdjustmentsTotal();

        $this->total = ($this->getQuantity() * $this->getUnitPrice()) + $this->adjustmentsTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotal($type = null, $includeNeutral = false)
    {
        if (null === $type && !$includeNeutral) {
            // By default the order will have calculated the 'standard' adjustments total so we just return it.
            return $this->adjustmentsTotal;
        }

        // Any non-standard requests for totals need to be calculated
        return $this->calculateAdjustmentsTotal($type, $includeNeutral);
    }

    /**
     * @param string|null $type
     *
     * @return Adjustment[]
     */
    public function getAdjustments($type = null)
    {
        $adjustments = [];

        foreach ($this->getInventoryUnits() as $inventoryUnit) {
            $adjustments = array_merge($adjustments, $inventoryUnit->getAdjustments($type)->toArray());
        }

        return $adjustments;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateAdjustmentsTotal($type = null, $includeNeutral = false)
    {
        $adjustmentsTotal = 0;

        foreach ($this->getInventoryUnits() as $inventoryUnit) {
            foreach ($inventoryUnit->getAdjustments($type) as $inventoryUnitAdjustment) {
                if ($includeNeutral || !$inventoryUnitAdjustment->isNeutral()) {
                    $adjustmentsTotal += $inventoryUnitAdjustment->getAmount();
                }
            }
        }

        return $adjustmentsTotal;
    }
}
