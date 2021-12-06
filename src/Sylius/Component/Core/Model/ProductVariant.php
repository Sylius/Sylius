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
use Doctrine\Common\Comparable;
use Sylius\Component\Product\Model\ProductVariant as BaseVariant;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

class ProductVariant extends BaseVariant implements ProductVariantInterface, Comparable
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
     * @var float|null
     */
    protected $weight;

    /**
     * @var float|null
     */
    protected $width;

    /**
     * @var float|null
     */
    protected $height;

    /**
     * @var float|null
     */
    protected $depth;

    /**
     * @var TaxCategoryInterface|null
     */
    protected $taxCategory;

    /**
     * @var ShippingCategoryInterface|null
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

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }

    public function isInStock(): bool
    {
        return 0 < $this->onHand;
    }

    public function getOnHold(): ?int
    {
        return $this->onHold;
    }

    public function setOnHold(?int $onHold): void
    {
        $this->onHold = $onHold;
    }

    public function getOnHand(): ?int
    {
        return $this->onHand;
    }

    public function setOnHand(?int $onHand): void
    {
        $this->onHand = (0 > $onHand) ? 0 : $onHand;
    }

    public function isTracked(): bool
    {
        return $this->tracked;
    }

    public function setTracked(bool $tracked): void
    {
        $this->tracked = $tracked;
    }

    public function getInventoryName(): ?string
    {
        return $this->getProduct()->getName();
    }

    public function getShippingCategory(): ?ShippingCategoryInterface
    {
        return $this->shippingCategory;
    }

    public function setShippingCategory(?ShippingCategoryInterface $category): void
    {
        $this->shippingCategory = $category;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): void
    {
        $this->weight = $weight;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): void
    {
        $this->height = $height;
    }

    public function getDepth(): ?float
    {
        return $this->depth;
    }

    public function setDepth(?float $depth): void
    {
        $this->depth = $depth;
    }

    public function getShippingWeight(): ?float
    {
        return $this->getWeight();
    }

    public function getShippingWidth(): ?float
    {
        return $this->getWidth();
    }

    public function getShippingHeight(): ?float
    {
        return $this->getHeight();
    }

    public function getShippingDepth(): ?float
    {
        return $this->getDepth();
    }

    public function getShippingVolume(): ?float
    {
        return $this->depth * $this->height * $this->width;
    }

    public function getTaxCategory(): ?TaxCategoryInterface
    {
        return $this->taxCategory;
    }

    public function setTaxCategory(?TaxCategoryInterface $category): void
    {
        $this->taxCategory = $category;
    }

    public function getChannelPricings(): Collection
    {
        return $this->channelPricings;
    }

    public function getChannelPricingForChannel(ChannelInterface $channel): ?ChannelPricingInterface
    {
        if ($this->channelPricings->containsKey($channel->getCode())) {
            return $this->channelPricings->get($channel->getCode());
        }

        return null;
    }

    public function hasChannelPricingForChannel(ChannelInterface $channel): bool
    {
        return null !== $this->getChannelPricingForChannel($channel);
    }

    public function hasChannelPricing(ChannelPricingInterface $channelPricing): bool
    {
        return $this->channelPricings->contains($channelPricing);
    }

    public function addChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if (!$this->hasChannelPricing($channelPricing)) {
            $channelPricing->setProductVariant($this);
            $this->channelPricings->set($channelPricing->getChannelCode(), $channelPricing);
        }
    }

    public function removeChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if ($this->hasChannelPricing($channelPricing)) {
            $channelPricing->setProductVariant(null);
            $this->channelPricings->remove($channelPricing->getChannelCode());
        }
    }

    public function isShippingRequired(): bool
    {
        return $this->shippingRequired;
    }

    public function setShippingRequired(bool $shippingRequired): void
    {
        $this->shippingRequired = $shippingRequired;
    }

    public function getAppliedPromotionsForChannel(ChannelInterface $channel): array
    {
        $channelPricing = $this->getChannelPricingForChannel($channel);

        return ($channelPricing !== null) ? $channelPricing->getAppliedPromotions() : [];
    }

    /**
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (ProductImageInterface $image) use ($type): bool {
            return $type === $image->getType();
        });
    }

    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }

    public function hasImage(ProductImageInterface $image): bool
    {
        return $this->images->contains($image);
    }

    public function addImage(ProductImageInterface $image): void
    {
        if ($this->hasImage($image)) {
            return;
        }
        $image->setOwner($this->getProduct());
        $image->addProductVariant($this);
        $this->images->add($image);
    }

    public function removeImage(ProductImageInterface $image): void
    {
        if ($this->hasImage($image)) {
            $this->images->removeElement($image);
        }
    }

    public function compareTo($other): int
    {
        return $this->code === $other->getCode() ? 0 : 1;
    }
}
