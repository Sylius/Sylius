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
use Sylius\Component\Product\Model\ProductVariant as BaseVariant;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariant extends BaseVariant implements ProductVariantInterface
{
    /**
     * @var int
     */
    protected $version = 1;

    /**
     * @var int
     */
    protected $onHold = 0;

    /**
     * @var int
     */
    protected $onHand = 0;

    /**
     * @var bool
     */
    protected $tracked = false;

    /**
     * @var float
     */
    protected $weight;

    /**
     * @var float
     */
    protected $width;

    /**
     * @var float
     */
    protected $height;

    /**
     * @var float
     */
    protected $depth;

    /**
     * @var TaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * @var ShippingCategoryInterface
     */
    protected $shippingCategory;

    /**
     * @var Collection
     */
    protected $channelPricings;

    /**
     * @var bool
     */
    protected $shippingRequired = true;

    /**
     * @var Collection|ProductImageInterface[]
     */
    protected $images;

    public function __construct()
    {
        parent::__construct();

        $this->channelPricings = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = (string) $this->getProduct()->getName();

        if (!$this->getOptionValues()->isEmpty()) {
            $string .= '(';

            foreach ($this->getOptionValues() as $option) {
                $string .= $option->getOption()->getName().': '.$option->getValue().', ';
            }

            $string = substr($string, 0, -2).')';
        }

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock(): bool
    {
        return 0 < $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHold(): ?int
    {
        return $this->onHold;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHold(?int $onHold): void
    {
        $this->onHold = $onHold;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand(): ?int
    {
        return $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand(?int $onHand): void
    {
        $this->onHand = (0 > $onHand) ? 0 : $onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function isTracked(): bool
    {
        return $this->tracked;
    }

    /**
     * {@inheritdoc}
     */
    public function setTracked(bool $tracked): void
    {
        $this->tracked = $tracked;
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryName(): ?string
    {
        return $this->getProduct()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCategory(): ?ShippingCategoryInterface
    {
        return $this->shippingCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingCategory(ShippingCategoryInterface $category = null)
    {
        $this->shippingCategory = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * {@inheritdoc}
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * {@inheritdoc}
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingWeight(): ?float
    {
        return $this->getWeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingWidth(): ?float
    {
        return $this->getWidth();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingHeight(): ?float
    {
        return $this->getHeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingDepth(): ?float
    {
        return $this->getDepth();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingVolume(): ?float
    {
        return $this->depth * $this->height * $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory(): ?TaxCategoryInterface
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(TaxCategoryInterface $category = null)
    {
        $this->taxCategory = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelPricings()
    {
        return $this->channelPricings;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelPricingForChannel(ChannelInterface $channel)
    {
        if ($this->channelPricings->containsKey($channel->getCode())) {
            return $this->channelPricings->get($channel->getCode());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannelPricingForChannel(ChannelInterface $channel)
    {
        return null !== $this->getChannelPricingForChannel($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannelPricing(ChannelPricingInterface $channelPricing)
    {
        return $this->channelPricings->contains($channelPricing);
    }

    /**
     * {@inheritdoc}
     */
    public function addChannelPricing(ChannelPricingInterface $channelPricing)
    {
        if (!$this->hasChannelPricing($channelPricing)) {
            $channelPricing->setProductVariant($this);
            $this->channelPricings->set($channelPricing->getChannelCode(), $channelPricing);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannelPricing(ChannelPricingInterface $channelPricing)
    {
        if ($this->hasChannelPricing($channelPricing)) {
            $channelPricing->setProductVariant(null);
            $this->channelPricings->remove($channelPricing->getChannelCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingRequired()
    {
        return $this->shippingRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingRequired($shippingRequired)
    {
        $this->shippingRequired = $shippingRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesByType($type)
    {
        return $this->images->filter(function (ProductImageInterface $image) use ($type) {
            return $type === $image->getType();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hasImages()
    {
        return !$this->images->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(ProductImageInterface $image)
    {
        return $this->images->contains($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ProductImageInterface $image)
    {
        if ($this->hasImage($image)) {
            return;
        }
        $image->setOwner($this->getProduct());
        $image->addProductVariant($this);
        $this->images->add($image);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(ProductImageInterface $image)
    {
        if ($this->hasImage($image)) {
            $this->images->removeElement($image);
        }
    }
}
