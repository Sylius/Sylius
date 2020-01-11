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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseVariantInterface;
use Sylius\Component\Resource\Model\VersionedInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

interface ProductVariantInterface extends
    BaseVariantInterface,
    ShippableInterface,
    StockableInterface,
    TaxableInterface,
    VersionedInterface,
    ProductImagesAwareInterface
{
    public function getWeight(): ?float;

    public function setWeight(?float $weight): void;

    public function getWidth(): ?float;

    public function setWidth(?float $width): void;

    public function getHeight(): ?float;

    public function setHeight(?float $height): void;

    public function getDepth(): ?float;

    public function setDepth(?float $depth): void;

    public function setTaxCategory(?TaxCategoryInterface $category): void;

    public function setShippingCategory(?ShippingCategoryInterface $shippingCategory): void;

    /**
     * @return Collection|ChannelPricingInterface[]
     *
     * @psalm-return Collection<array-key, ChannelPricingInterface>
     */
    public function getChannelPricings(): Collection;

    public function getChannelPricingForChannel(ChannelInterface $channel): ?ChannelPricingInterface;

    public function hasChannelPricingForChannel(ChannelInterface $channel): bool;

    public function hasChannelPricing(ChannelPricingInterface $channelPricing): bool;

    public function addChannelPricing(ChannelPricingInterface $channelPricing): void;

    public function removeChannelPricing(ChannelPricingInterface $channelPricing): void;

    public function isShippingRequired(): bool;

    public function setShippingRequired(bool $shippingRequired): void;
}
