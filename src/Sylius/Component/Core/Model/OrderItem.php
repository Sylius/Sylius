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
use Webmozart\Assert\Assert;

class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    /** @var int */
    protected $version = 1;

    /** @var ProductVariantInterface|null */
    protected $variant;

    /** @var string|null */
    protected $productName;

    /** @var string|null */
    protected $variantName;

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }

    public function getVariant(): ?ProductVariantInterface
    {
        return $this->variant;
    }

    public function setVariant(?ProductVariantInterface $variant): void
    {
        $this->variant = $variant;
    }

    public function getProduct(): ?ProductInterface
    {
        return $this->variant->getProduct();
    }

    public function getProductName(): ?string
    {
        return $this->productName ?: $this->variant->getProduct()->getName();
    }

    public function setProductName(?string $productName): void
    {
        $this->productName = $productName;
    }

    public function getVariantName(): ?string
    {
        return $this->variantName ?: $this->variant->getName();
    }

    public function setVariantName(?string $variantName): void
    {
        $this->variantName = $variantName;
    }

    public function equals(BaseOrderItemInterface $orderItem): bool
    {
        return parent::equals($orderItem) || ($orderItem instanceof static && $orderItem->getVariant() === $this->variant);
    }

    /**
     * Returns sum of neutral and non neutral tax adjustments on order item and total tax of units.
     */
    public function getTaxTotal(): int
    {
        $taxTotal = 0;

        foreach ($this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            $taxTotal += $taxAdjustment->getAmount();
        }

        foreach ($this->units as $unit) {
            /** @var OrderItemUnitInterface $unit */
            Assert::isInstanceOf($unit, OrderItemUnitInterface::class);

            $taxTotal += $unit->getTaxTotal();
        }

        return $taxTotal;
    }

    /**
     * Returns single unit price lowered by order unit promotions (each unit must have the same unit promotion discount)
     */
    public function getDiscountedUnitPrice(): int
    {
        if ($this->units->isEmpty()) {
            return $this->unitPrice;
        }

        $firstUnit = $this->units->first();

        /** @var OrderItemUnitInterface $firstUnit */
        Assert::isInstanceOf($firstUnit, OrderItemUnitInterface::class);

        return
            $this->unitPrice +
            $firstUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
        ;
    }

    public function getFullDiscountedUnitPrice(): int
    {
        if ($this->units->isEmpty()) {
            return $this->unitPrice;
        }

        $firstUnit = $this->units->first();

        /** @var OrderItemUnitInterface $firstUnit */
        Assert::isInstanceOf($firstUnit, OrderItemUnitInterface::class);

        return
            $this->unitPrice +
            $firstUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT) +
            $firstUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
        ;
    }

    public function getSubtotal(): int
    {
        return $this->getDiscountedUnitPrice() * $this->quantity;
    }
}
