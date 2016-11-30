<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseVariantInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantInterface extends
    BaseVariantInterface,
    ShippableInterface,
    StockableInterface,
    TaxableInterface
{
    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $weight
     */
    public function setWeight($weight);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @param float $width
     */
    public function setWidth($width);

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @param float $height
     */
    public function setHeight($height);

    /**
     * @return float
     */
    public function getDepth();

    /**
     * @param float $depth
     */
    public function setDepth($depth);

    /**
     * @param TaxCategoryInterface $category
     */
    public function setTaxCategory(TaxCategoryInterface $category = null);

    /**
     * @param ShippingCategoryInterface $shippingCategory
     */
    public function setShippingCategory(ShippingCategoryInterface $shippingCategory);

    /**
     * @return Collection|ChannelPricingInterface[]
     */
    public function getChannelPricings();

    /**
     * @param ChannelInterface $channel
     *
     * @return ChannelPricingInterface|null
     */
    public function getChannelPricingForChannel(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    public function hasChannelPricingForChannel(ChannelInterface $channel);

    /**
     * @param ChannelPricingInterface $channelPricing
     *
     * @return bool
     */
    public function hasChannelPricing(ChannelPricingInterface $channelPricing);

    /**
     * @param ChannelPricingInterface $channelPricing
     */
    public function addChannelPricing(ChannelPricingInterface $channelPricing);

    /**
     * @param ChannelPricingInterface $channelPricing
     */
    public function removeChannelPricing(ChannelPricingInterface $channelPricing);
}
