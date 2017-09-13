<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderItem as BaseOrderItem;
use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;

class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    /**
     * @var ProductVariantInterface
     */
    protected $variant;

    /**
     * @return string
     */
    public function getImmutableVariantName(): ?string
    {
        return $this->immutableVariantName ?: $this->variant->getName();
    }

    /**
     * @param string $immutableVariantName
     */
    public function setImmutableVariantName(?string $immutableVariantName)
    {
        $this->immutableVariantName = $immutableVariantName;
    }

    /**
     * @return string
     */
    public function getImmutableVariantCode(): ?string
    {
        return $this->immutableVariantCode ?: $this->variant->getCode();
    }

    /**
     * @param string $immutableVariantCode
     */
    public function setImmutableVariantCode(?string $immutableVariantCode)
    {
        $this->immutableVariantCode = $immutableVariantCode;
    }

    /**
     * @return string
     */
    public function getImmutableProductName(): ?string
    {
        return $this->immutableProductName ?: $this->variant->getProduct()->getName();
    }

    /**
     * @param string $immutableProductName
     */
    public function setImmutableProductName(?string $immutableProductName)
    {
        $this->immutableProductName = $immutableProductName;
    }

    /**
     * @return string
     */
    public function getImmutableProductCode(): ?string
    {
        return $this->immutableProductCode ?: $this->variant->getProduct()->getCode();
    }

    /**
     * @param string $immutableProductCode
     */
    public function setImmutableProductCode(?string $immutableProductCode)
    {
        $this->immutableProductCode = $immutableProductCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(?ProductVariantInterface $variant): void
    {
        if(isset($variant)) {

            $this->setImmutableProductName($variant->getProduct()->getName());
            $this->setImmutableVariantName($variant->getName());

            $this->setImmutableProductCode($variant->getProduct()->getCode());
            $this->setImmutableVariantCode($variant->getCode());

        }

        $this->variant = $variant;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariant(): ?ProductVariantInterface
    {
        $variant = $this->variant;

        if(isset($variant)) {

            $variant->setName($this->getImmutableVariantName());
            $variant->setCode($this->getImmutableVariantCode());

            $product = $variant->getProduct();
            $product->setName($this->getImmutableProductName());
            $product->setCode($this->getImmutableProductCode());
        }

        return $variant;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct(): ?ProductInterface
    {
        return $this->variant->getProduct();
    }


    /**
     * {@inheritdoc}
     */
    public function equals(BaseOrderItemInterface $item): bool
    {
        return parent::equals($item) || ($item instanceof static && $item->getVariant() === $this->variant);
    }

    /**
     * Returns sum of neutral and non neutral tax adjustments on order item and total tax of units.
     *
     * {@inheritdoc}
     */
    public function getTaxTotal(): int
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
    public function getDiscountedUnitPrice(): int
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
    public function getSubtotal(): int
    {
        return $this->getDiscountedUnitPrice() * $this->quantity;
    }
}
