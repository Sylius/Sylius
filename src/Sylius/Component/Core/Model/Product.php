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
use Doctrine\Common\Collections\Criteria;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Product\Model\Product as BaseProduct;
use Sylius\Component\Product\Model\ProductTranslationInterface as BaseProductTranslationInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class Product extends BaseProduct implements ProductInterface, ReviewableProductInterface
{
    /**
     * @var string
     */
    protected $variantSelectionMethod = self::VARIANT_SELECTION_CHOICE;

    /**
     * @var Collection|ProductTaxonInterface[]
     */
    protected $productTaxons;

    /**
     * @var Collection|ChannelInterface[]
     */
    protected $channels;

    /**
     * @var BaseTaxonInterface
     */
    protected $mainTaxon;

    /**
     * @var Collection|ReviewInterface[]
     */
    protected $reviews;

    /**
     * @var float
     */
    protected $averageRating = 0;

    /**
     * @var Collection|ImageInterface[]
     */
    protected $images;

    public function __construct()
    {
        parent::__construct();

        $this->productTaxons = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantSelectionMethod()
    {
        return $this->variantSelectionMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariantSelectionMethod($variantSelectionMethod)
    {
        if (!in_array($variantSelectionMethod, [self::VARIANT_SELECTION_CHOICE, self::VARIANT_SELECTION_MATCH])) {
            throw new \InvalidArgumentException(sprintf('Wrong variant selection method "%s" given.', $variantSelectionMethod));
        }

        $this->variantSelectionMethod = $variantSelectionMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function isVariantSelectionMethodChoice()
    {
        return self::VARIANT_SELECTION_CHOICE === $this->variantSelectionMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantSelectionMethodLabel()
    {
        $labels = self::getVariantSelectionMethodLabels();

        return $labels[$this->variantSelectionMethod];
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTaxons()
    {
        return $this->productTaxons;
    }

    /**
     * {@inheritdoc}
     */
    public function addProductTaxon(ProductTaxonInterface $productTaxon)
    {
        if (!$this->hasProductTaxon($productTaxon)) {
            $this->productTaxons->add($productTaxon);
            $productTaxon->setProduct($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductTaxon(ProductTaxonInterface $productTaxon)
    {
        if ($this->hasProductTaxon($productTaxon)) {
            $this->productTaxons->removeElement($productTaxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductTaxon(ProductTaxonInterface $productTaxon)
    {
        return $this->productTaxons->contains($productTaxon);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxons()
    {
        return $this->productTaxons->map(function (ProductTaxonInterface $productTaxon) {
            return $productTaxon->getTaxon();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxon(TaxonInterface $taxon)
    {
        return $this->getTaxons()->contains($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    /**
     * {@inheritdoc}
     */
    public static function getVariantSelectionMethodLabels()
    {
        return [
            self::VARIANT_SELECTION_CHOICE => 'sylius.ui.variant_choice',
            self::VARIANT_SELECTION_MATCH => 'sylius.ui.options_matching',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        return $this->getTranslation()->getShortDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription)
    {
        $this->getTranslation()->setShortDescription($shortDescription);
    }

    /**
     * {@inheritdoc}
     */
    public function getMainTaxon()
    {
        return $this->mainTaxon;
    }

    /**
     * {@inheritdoc}
     */
    public function setMainTaxon(TaxonInterface $mainTaxon = null)
    {
        $this->mainTaxon = $mainTaxon;
    }

    /**
     * {@inheritdoc}
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * {@inheritdoc}
     */
    public function getAcceptedReviews()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('status', ReviewInterface::STATUS_ACCEPTED))
        ;

        return $this->reviews->matching($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function addReview(ReviewInterface $review): void
    {
        $this->reviews->add($review);
    }

    /**
     * {@inheritdoc}
     */
    public function removeReview(ReviewInterface $review): void
    {
        $this->reviews->remove($review);
    }

    /**
     * {@inheritdoc}
     */
    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    /**
     * {@inheritdoc}
     */
    public function setAverageRating(float $averageRating): void
    {
        $this->averageRating = $averageRating;
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
        return $this->images->filter(function (ImageInterface $image) use ($type) {
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
    public function hasImage(ImageInterface $image)
    {
        return $this->images->contains($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ImageInterface $image)
    {
        $image->setOwner($this);
        $this->images->add($image);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(ImageInterface $image)
    {
        if ($this->hasImage($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): BaseProductTranslationInterface
    {
        return new ProductTranslation();
    }
}
