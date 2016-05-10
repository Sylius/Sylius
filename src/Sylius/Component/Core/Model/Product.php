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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Product\Model\Product as BaseProduct;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
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
    protected $variantSelectionMethod;

    /**
     * @var Collection|BaseTaxonInterface[]
     */
    protected $taxons;

    /**
     * @var ShippingCategoryInterface
     */
    protected $shippingCategory;

    /**
     * @var ZoneInterface
     */
    protected $restrictedZone;

    /**
     * @var ChannelInterface[]|Collection
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

    public function __construct()
    {
        parent::__construct();

        $this->taxons = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->reviews = new ArrayCollection();

        $this->variantSelectionMethod = self::VARIANT_SELECTION_CHOICE;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataClassIdentifier()
    {
        return self::METADATA_CLASS_IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataIdentifier()
    {
        return $this->getMetadataClassIdentifier().'-'.$this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getMasterVariant()->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        $this->getMasterVariant()->setSku($sku);
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
    public function getTaxons($taxonomy = null)
    {
        if (null !== $taxonomy) {
            return $this->taxons->filter(function (BaseTaxonInterface $taxon) use ($taxonomy) {
                return $taxonomy === strtolower($taxon->getTaxonomy()->getName());
            });
        }

        return $this->taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxons(Collection $taxons)
    {
        $this->taxons = $taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function addTaxon(BaseTaxonInterface $taxon)
    {
        if (!$this->hasTaxon($taxon)) {
            $this->taxons->add($taxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxon(BaseTaxonInterface $taxon)
    {
        if ($this->hasTaxon($taxon)) {
            $this->taxons->removeElement($taxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxon(BaseTaxonInterface $taxon)
    {
        return $this->taxons->contains($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        if (null === $this->getMasterVariant()) {
            return 0;
        }

        return $this->getMasterVariant()->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->getMasterVariant()->setPrice($price);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCategory()
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
    public function getRestrictedZone()
    {
        return $this->restrictedZone;
    }

    /**
     * {@inheritdoc}
     */
    public function setRestrictedZone(ZoneInterface $zone = null)
    {
        $this->restrictedZone = $zone;
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->getMasterVariant()->getImages();
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getMasterVariant()->getImages()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannels(Collection $channels)
    {
        $this->channels = $channels;
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(BaseChannelInterface $channel)
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(BaseChannelInterface $channel)
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(BaseChannelInterface $channel)
    {
        return $this->channels->contains($channel);
    }

    /**
     * {@inheritdoc}
     */
    public static function getVariantSelectionMethodLabels()
    {
        return [
            self::VARIANT_SELECTION_CHOICE => 'Variant choice',
            self::VARIANT_SELECTION_MATCH => 'Options matching',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        return $this->translate()->getShortDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription)
    {
        $this->translate()->setShortDescription($shortDescription);
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
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * {@inheritdoc}
     */
    public function addReview(ReviewInterface $review)
    {
        $this->reviews->add($review);
    }

    /**
     * {@inheritdoc}
     */
    public function removeReview(ReviewInterface $review)
    {
        $this->reviews->remove($review);
    }

    /**
     * {@inheritdoc}
     */
    public function setAverageRating($averageRating)
    {
        $this->averageRating = $averageRating;
    }

    /**
     * {@inheritdoc}
     */
    public function getAverageRating()
    {
        return $this->averageRating;
    }
}
