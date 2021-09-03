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
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Product\Model\Product as BaseProduct;
use Sylius\Component\Product\Model\ProductTranslationInterface as BaseProductTranslationInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;
use Webmozart\Assert\Assert;

class Product extends BaseProduct implements ProductInterface, ReviewableProductInterface
{
    protected ?string $variantSelectionMethod = self::VARIANT_SELECTION_CHOICE;

    /**
     * @var Collection|ProductTaxonInterface[]
     *
     * @psalm-var Collection<array-key, ProductTaxonInterface>
     */
    protected Collection $productTaxons;

    /**
     * @var Collection|ChannelInterface[]
     *
     * @psalm-var Collection<array-key, ChannelInterface>
     */
    protected Collection $channels;

    protected ?TaxonInterface $mainTaxon = null;

    /**
     * @var Collection|ReviewInterface[]
     *
     * @psalm-var Collection<array-key, ReviewInterface>
     */
    protected Collection $reviews;

    protected float $averageRating = 0.0;

    /**
     * @var Collection|ImageInterface[]
     *
     * @psalm-var Collection<array-key, ImageInterface>
     */
    protected Collection $images;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, ProductTaxonInterface> $this->productTaxons */
        $this->productTaxons = new ArrayCollection();

        /** @var ArrayCollection<array-key, ChannelInterface> $this->channels */
        $this->channels = new ArrayCollection();

        /** @var ArrayCollection<array-key, ReviewInterface> $this->reviews */
        $this->reviews = new ArrayCollection();

        /** @var ArrayCollection<array-key, ImageInterface> $this->images */
        $this->images = new ArrayCollection();
    }

    public function getVariantSelectionMethod(): string
    {
        return $this->variantSelectionMethod;
    }

    public function setVariantSelectionMethod(?string $variantSelectionMethod): void
    {
        Assert::oneOf(
            $variantSelectionMethod,
            [self::VARIANT_SELECTION_CHOICE, self::VARIANT_SELECTION_MATCH],
            sprintf('Wrong variant selection method "%s" given.', $variantSelectionMethod)
        );

        $this->variantSelectionMethod = $variantSelectionMethod;
    }

    public function isVariantSelectionMethodChoice(): bool
    {
        return self::VARIANT_SELECTION_CHOICE === $this->variantSelectionMethod;
    }

    public function getVariantSelectionMethodLabel(): string
    {
        $labels = self::getVariantSelectionMethodLabels();

        return $labels[$this->variantSelectionMethod];
    }

    public function getProductTaxons(): Collection
    {
        return $this->productTaxons;
    }

    public function addProductTaxon(ProductTaxonInterface $productTaxon): void
    {
        if (!$this->hasProductTaxon($productTaxon)) {
            $this->productTaxons->add($productTaxon);
            $productTaxon->setProduct($this);
        }
    }

    public function removeProductTaxon(ProductTaxonInterface $productTaxon): void
    {
        if ($this->hasProductTaxon($productTaxon)) {
            $this->productTaxons->removeElement($productTaxon);
        }
    }

    public function hasProductTaxon(ProductTaxonInterface $productTaxon): bool
    {
        return $this->productTaxons->contains($productTaxon);
    }

    public function getTaxons(): Collection
    {
        return $this->productTaxons->map(function (ProductTaxonInterface $productTaxon): TaxonInterface {
            return $productTaxon->getTaxon();
        });
    }

    public function hasTaxon(TaxonInterface $taxon): bool
    {
        return $this->getTaxons()->contains($taxon);
    }

    /**
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function getShortDescription(): ?string
    {
        return $this->getTranslation()->getShortDescription();
    }

    public function setShortDescription(?string $shortDescription): void
    {
        $this->getTranslation()->setShortDescription($shortDescription);
    }

    public function getMainTaxon(): ?TaxonInterface
    {
        return $this->mainTaxon;
    }

    public function setMainTaxon(?TaxonInterface $mainTaxon): void
    {
        $this->mainTaxon = $mainTaxon;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function getAcceptedReviews(): Collection
    {
        return $this->reviews->filter(function (ReviewInterface $review): bool {
            return ReviewInterface::STATUS_ACCEPTED === $review->getStatus();
        });
    }

    public function addReview(ReviewInterface $review): void
    {
        $this->reviews->add($review);
    }

    public function removeReview(ReviewInterface $review): void
    {
        $this->reviews->removeElement($review);
    }

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    public function setAverageRating(float $averageRating): void
    {
        $this->averageRating = $averageRating;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (ImageInterface $image) use ($type): bool {
            return $type === $image->getType();
        });
    }

    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }

    public function hasImage(ImageInterface $image): bool
    {
        return $this->images->contains($image);
    }

    public function addImage(ImageInterface $image): void
    {
        $image->setOwner($this);
        $this->images->add($image);
    }

    public function removeImage(ImageInterface $image): void
    {
        if ($this->hasImage($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
    }

    public static function getVariantSelectionMethodLabels(): array
    {
        return [
            self::VARIANT_SELECTION_CHOICE => 'sylius.ui.variant_choice',
            self::VARIANT_SELECTION_MATCH => 'sylius.ui.options_matching',
        ];
    }

    /**
     * @return ProductTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        return parent::getTranslation($locale);
    }

    /**
     * @return ProductTranslationInterface
     */
    protected function createTranslation(): BaseProductTranslationInterface
    {
        return new ProductTranslation();
    }
}
