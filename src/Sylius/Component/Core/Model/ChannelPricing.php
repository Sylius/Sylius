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

class ChannelPricing implements ChannelPricingInterface
{
    /** @var mixed */
    protected $id = null;

    /**
     * @var string|null
     */
    protected $channelCode;

    /**
     * @var ProductVariantInterface|null
     */
    protected $productVariant;

    /**
     * @var int|null
     */
    protected $price;

    /**
     * @var int|null
     */
    protected $originalPrice;

    /**
     * @var int
     */
    protected $minimumPrice = 0;

    /** @var ?array */
    protected $appliedPromotions = [];

    public function __toString(): string
    {
        return (string) $this->getPrice();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }

    public function getProductVariant(): ?ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function setProductVariant(?ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    public function getOriginalPrice(): ?int
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?int $originalPrice): void
    {
        $this->originalPrice = $originalPrice;
    }

    public function isPriceReduced(): bool
    {
        return $this->originalPrice > $this->price;
    }

    public function getMinimumPrice(): int
    {
        return $this->minimumPrice;
    }

    public function setMinimumPrice(?int $minimumPrice): void
    {
        $this->minimumPrice = $minimumPrice ?: 0;
    }

    public function addAppliedPromotion(array $promotion): void
    {
        if ($this->appliedPromotions === null) {
            $this->appliedPromotions = $promotion;

            return;
        }

        $this->appliedPromotions = array_merge($this->appliedPromotions, $promotion);
    }

    public function removeAppliedPromotion(string $promotionCode): void
    {
        unset($this->appliedPromotions[$promotionCode]);
    }

    public function getAppliedPromotions(): array
    {
        return $this->appliedPromotions;
    }

    public function clearAppliedPromotions(): void
    {
        $this->appliedPromotions = [];
    }

    public function hasExclusiveCatalogPromotionApplied(): bool
    {
        if ($this->appliedPromotions === []) {
            return false;
        }

        if (reset($this->appliedPromotions)['is_exclusive'])
        {
            return true;
        }

        return false;
    }
}
