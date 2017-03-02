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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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
     * {@inheritdoc}
     */
    function __toString()
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
}
