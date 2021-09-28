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

    protected ?string $channelCode = null;

    protected ?ProductVariantInterface $productVariant = null;

    protected ?int $price = null;

    protected ?int $originalPrice = null;

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
}
