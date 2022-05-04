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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ChannelPricing implements ChannelPricingInterface, \Stringable
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $channelCode;

    /** @var ProductVariantInterface|null */
    protected $productVariant;

    /** @var int|null */
    protected $price;

    /** @var int|null */
    protected $originalPrice;

    /**
     * @var int
     */
    protected $minimumPrice = 0;

    /**
     * @var ArrayCollection
     * @psalm-var ArrayCollection<array-key, CatalogPromotionInterface>
     */
    protected $appliedPromotions;

    public function __construct()
    {
        $this->appliedPromotions = new ArrayCollection();
    }

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

    public function getAppliedPromotions(): Collection
    {
        return $this->appliedPromotions;
    }

    public function addAppliedPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        if($this->appliedPromotions->contains($catalogPromotion)) {
            return;
        }

        $this->appliedPromotions->add($catalogPromotion);
    }

    public function removeAppliedPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->appliedPromotions->removeElement($catalogPromotion);
    }

    public function hasPromotionApplied(CatalogPromotionInterface $catalogPromotion): bool
    {
        return $this->appliedPromotions->contains($catalogPromotion);
    }

    public function clearAppliedPromotions(): void
    {
        $this->appliedPromotions->clear();
    }

    public function hasExclusiveCatalogPromotionApplied(): bool
    {
        foreach ($this->appliedPromotions as $appliedPromotion) {
            if($appliedPromotion->isExclusive()) {
                return true;
            }
        }

        return false;
    }
}
