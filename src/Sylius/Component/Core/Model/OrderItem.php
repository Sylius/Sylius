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
     * @var string
     */
    protected $immutableProductName;

    /**
     * @var string
     */
    protected $immutableProductCode;

    /**
     * @var string
     */
    protected $immutableVariantName;

    /**
     * @var string
     */
    protected $immutableVariantCode;

    /**
     * {@inheritdoc}
     */
    public function getVariant(): ?ProductVariantInterface
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(?ProductVariantInterface $variant): void
    {
        /** @var OrderInterface $order */
        $order = $this->getOrder();
        $localeCode = $order ? $order->getLocaleCode() : null;

        if (null !== $variant) {
            $this->setImmutableVariantName($variant->getTranslation($localeCode)->getName());
            $this->setImmutableVariantCode($variant->getCode());
        }

        if (null !== $variant && null !== $variant->getProduct()) {
            $this->setImmutableProductName($variant->getProduct()->getTranslation($localeCode)->getName());
            $this->setImmutableProductCode($variant->getProduct()->getCode());
        }

        $this->variant = $variant;
    }

    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface
    {
        return $this->variant->getProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getImmutableProductName(): ?string
    {
        return $this->immutableProductName ?: $this->variant->getProduct()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setImmutableProductName(?string $immutableProductName): void
    {
        $this->immutableProductName = $immutableProductName;
    }

    /**
     * {@inheritdoc}
     */
    public function getImmutableProductCode(): ?string
    {
        return $this->immutableProductCode ?: $this->variant->getProduct()->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setImmutableProductCode(?string $immutableProductCode): void
    {
        $this->immutableProductCode = $immutableProductCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getImmutableVariantName(): ?string
    {
        return $this->immutableVariantName ?: $this->variant->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setImmutableVariantName(?string $immutableVariantName): void
    {
        $this->immutableVariantName = $immutableVariantName;
    }

    /**
     * {@inheritdoc}
     */
    public function getImmutableVariantCode(): ?string
    {
        return $this->immutableVariantCode ?: $this->variant->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setImmutableVariantCode(?string $immutableVariantCode): void
    {
        $this->immutableVariantCode = $immutableVariantCode;
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
