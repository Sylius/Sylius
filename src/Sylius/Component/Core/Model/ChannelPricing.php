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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class ChannelPricing implements ChannelPricingInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $channelCode;

    /**
     * @var ProductVariantInterface
     */
    protected $productVariant;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var int
     */
    protected $originalPrice;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelCode()
    {
        return $this->channelCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannelCode($channelCode)
    {
        $this->channelCode = $channelCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductVariant(ProductVariantInterface $productVariant = null)
    {
        $this->productVariant = $productVariant;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function isPriceReduced()
    {
        return $this->originalPrice > $this->price;
    }
}
