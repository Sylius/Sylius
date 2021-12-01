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

class ChannelPricing implements ChannelPricingInterface
{
    /** @var mixed */
    protected $id = null;

    protected ?string $channelCode;

    protected ?ProductVariantInterface $productVariant;

    protected ?int $price;
    /**
    * @var int|null
    */
    protected $originalPrice;

    protected ?int $minimumPrice;

    protected ?ArrayCollection $appliedPromotions;

    protected ?CatalogPromotionInterface $catalogPromotion;


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

    public function getMinimumPrice(): ?int
    {
        return $this->minimumPrice;
    }

    public function setMinimumPrice(?int $minimumPrice): void
    {
        $this->minimumPrice = $minimumPrice;
    }

    public function addAppliedPromotion(CatalogPromotionInterface $promotion): void
    {
            $this->appliedPromotions->add($promotion);
    }

    public function removeAppliedPromotion(CatalogPromotionInterface $promotion): void
    {
        $this->appliedPromotions->removeElement($promotion);
    }

    public function getAppliedPromotions(): ArrayCollection
    {
        return $this->appliedPromotions;
    }

    public function clearAppliedPromotions(): void
    {
        $this->appliedPromotions->clear();
    }

    public function hasExclusiveCatalogPromotionApplied(): bool
    {
        if ($this->appliedPromotions->isEmpty()) {
            return false;
        }

        foreach ($this->appliedPromotions as $promotion){
            if($promotion->isExclusive()) {
                return true;
            }
        }

        return false;
    }
}
