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

use Sylius\Component\Resource\Model\ResourceInterface;

interface ChannelPricingInterface extends ResourceInterface
{
    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(?ProductVariantInterface $productVariant): void;

    public function getPrice(): ?int;

    public function setPrice(?int $price): void;

    /**
     * @return string
     */
    public function getChannelCode(): ?string;

    public function setChannelCode(?string $channelCode): void;

    public function getOriginalPrice(): ?int;

    public function setOriginalPrice(?int $originalPrice): void;

    public function getMinimumPrice(): ?int;

    public function setMinimumPrice(?int $minimumPrice): void;

    public function isPriceReduced(): bool;

    public function addAppliedPromotion(array $promotion): void;

    public function removeAppliedPromotion(string $promotionCode): void;

    public function getAppliedPromotions(): array;

    public function clearAppliedPromotions(): void;

    public function hasExclusiveCatalogPromotionApplied(): bool;
}
