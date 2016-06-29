<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Cart\Model\CartItem;
use Sylius\Order\Model\OrderItemInterface as BaseOrderItemInterface;

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
     * Returns sum of neutral and non neutral tax adjustments on order item and total tax of units.
     *
     * {@inheritdoc}
     */
    public function getTaxTotal()
    {
        $taxTotal = 0;

        foreach ($this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            $taxTotal += $taxAdjustment->getAmount();
        }

        foreach ($this->units as $unit) {
            $taxTotal += $unit->getTaxTotal();
        }

        return $taxTotal;
    }

    /**
     * Returns single unit price lowered by order unit promotions (each unit must have the same unit promotion discount)
     *
     * {@inheritdoc}
     */
    public function getDiscountedUnitPrice()
    {
        if ($this->units->isEmpty()) {
            return $this->unitPrice;
        }

        return
            $this->unitPrice +
            $this->units->first()->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal()
    {
        return $this->getDiscountedUnitPrice() * $this->quantity;
    }
}
