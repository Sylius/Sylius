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
    /**
     * @return float|null
     */
    public function getWeight(): ?float;

    /**
     * @param float|null $weight
     */
    public function setWeight(?float $weight): void;

    /**
     * @return float|null
     */
    public function getWidth(): ?float;

    /**
     * @param float|null $width
     */
    public function setWidth(?float $width): void;

    /**
     * @return float|null
     */
    public function getHeight(): ?float;

    /**
     * @param float|null $height
     */
    public function setHeight(?float $height): void;

    /**
     * @return float|null
     */
    public function getDepth(): ?float;

    /**
     * @param float|null $depth
     */
    public function setDepth(?float $depth): void;

    /**
     * @param TaxCategoryInterface|null $category
     */
    public function setTaxCategory(?TaxCategoryInterface $category): void;

    /**
     * @param ShippingCategoryInterface|null $shippingCategory
     */
    public function setShippingCategory(?ShippingCategoryInterface $shippingCategory): void;

    /**
     * @return Collection|ChannelPricingInterface[]
     */
    public function getChannelPricings(): Collection;

    /**
     * @param ChannelInterface $channel
     *
     * @return ChannelPricingInterface|null
     */
    public function getChannelPricingForChannel(ChannelInterface $channel): ?ChannelPricingInterface;

    /**
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    public function hasChannelPricingForChannel(ChannelInterface $channel): bool;

    /**
     * @param ChannelPricingInterface $channelPricing
     *
     * @return bool
     */
    public function hasChannelPricing(ChannelPricingInterface $channelPricing): bool;

    /**
     * @param ChannelPricingInterface $channelPricing
     */
    public function addChannelPricing(ChannelPricingInterface $channelPricing): void;

    /**
     * @param ChannelPricingInterface $channelPricing
     */
    public function removeChannelPricing(ChannelPricingInterface $channelPricing): void;

    /**
     * @return bool
     */
    public function isShippingRequired(): bool;

    /**
     * @param bool $shippingRequired
     */
    public function setShippingRequired(bool $shippingRequired): void;
}
