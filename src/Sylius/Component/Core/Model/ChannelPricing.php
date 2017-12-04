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
    public function __toString(): string
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
    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductVariant(): ?ProductVariantInterface
    {
        return $this->productVariant;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductVariant(?ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalPrice(): ?int
    {
        return $this->originalPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalPrice(?int $originalPrice): void
    {
        $this->originalPrice = $originalPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function isPriceReduced(): bool
    {
        return $this->originalPrice > $this->price;
    }
}
