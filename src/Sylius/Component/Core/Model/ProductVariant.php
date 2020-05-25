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

class ProductVariant extends BaseVariant implements ProductVariantInterface
{
    /** @var int */
    protected $version = 1;

    /** @var int */
    protected $onHold = 0;

    /** @var int */
    protected $onHand = 0;

    /** @var bool */
    protected $tracked = false;

    /** @var float */
    protected $weight;

    /** @var float */
    protected $width;

    /** @var float */
    protected $height;

    /** @var float */
    protected $depth;

    /** @var TaxCategoryInterface */
    protected $taxCategory;

    /** @var ShippingCategoryInterface */
    protected $shippingCategory;

    /** @var Collection */
    protected $channelPricings;

    /** @var bool */
    protected $shippingRequired = true;

    /**
     * @var Collection|ProductImageInterface[]
     *
     * @psalm-var Collection<array-key, ProductImageInterface>
     */
    protected $images;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, ChannelPricingInterface> $this->channelPricings */
        $this->channelPricings = new ArrayCollection();

        /** @var ArrayCollection<array-key, ProductImageInterface> $this->images */
        $this->images = new ArrayCollection();
    }

    public function __toString(): string
    {
        $string = (string) $this->getProduct()->getName();

        if (!$this->getOptionValues()->isEmpty()) {
            $string .= '(';

            foreach ($this->getOptionValues() as $option) {
                $string .= $option->getOption()->getName() . ': ' . $option->getValue() . ', ';
            }

            $string = substr($string, 0, -2) . ')';
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
    public function setShippingCategory(?ShippingCategoryInterface $category): void
    {
        $this->shippingCategory = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight(?float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function setWidth(?float $width): void
    {
        $this->width = $width;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight(): ?float
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeight(?float $height): void
    {
        $this->height = $height;
    }

    /**
     * {@inheritdoc}
     */
    public function getDepth(): ?float
    {
        return $this->depth;
    }

    /**
     * {@inheritdoc}
     */
    public function setDepth(?float $depth): void
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
    public function setTaxCategory(?TaxCategoryInterface $category): void
    {
        $this->taxCategory = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelPricings(): Collection
    {
        return $this->channelPricings;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelPricingForChannel(ChannelInterface $channel): ?ChannelPricingInterface
    {
        if ($this->channelPricings->containsKey($channel->getCode())) {
            return $this->channelPricings->get($channel->getCode());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannelPricingForChannel(ChannelInterface $channel): bool
    {
        return null !== $this->getChannelPricingForChannel($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannelPricing(ChannelPricingInterface $channelPricing): bool
    {
        return $this->channelPricings->contains($channelPricing);
    }

    /**
     * {@inheritdoc}
     */
    public function addChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if (!$this->hasChannelPricing($channelPricing)) {
            $channelPricing->setProductVariant($this);
            $this->channelPricings->set($channelPricing->getChannelCode(), $channelPricing);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if ($this->hasChannelPricing($channelPricing)) {
            $channelPricing->setProductVariant(null);
            $this->channelPricings->remove($channelPricing->getChannelCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingRequired(): bool
    {
        return $this->shippingRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingRequired(bool $shippingRequired): void
    {
        $this->shippingRequired = $shippingRequired;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (ProductImageInterface $image) use ($type): bool {
            return $type === $image->getType();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(ProductImageInterface $image): bool
    {
        return $this->images->contains($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ProductImageInterface $image): void
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
    public function removeImage(ProductImageInterface $image): void
    {
        if ($this->hasImage($image)) {
            $this->images->removeElement($image);
        }
    }
}
