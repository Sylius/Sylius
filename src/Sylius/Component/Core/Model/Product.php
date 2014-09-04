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
use Sylius\Component\Product\Model\Product as BaseProduct;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

/**
 * Sylius core product entity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Product extends BaseProduct implements ProductInterface
{
    /**
     * Short product description.
     * For lists displaying.
     *
     * @var string
     */
    protected $shortDescription;

    /**
     * Variant selection method.
     *
     * @var string
     */
    protected $variantSelectionMethod;

    /**
     * Taxons.
     *
     * @var Collection|BaseTaxonInterface[]
     */
    protected $taxons;

    /**
     * Tax category.
     *
     * @var TaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * Shipping category.
     *
     * @var ShippingCategoryInterface
     */
    protected $shippingCategory;

    /**
     * Not allowed to ship in this zone.
     *
     * @var ZoneInterface
     */
    protected $restrictedZone;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setMasterVariant(new ProductVariant());
        $this->taxons = new ArrayCollection();

        $this->variantSelectionMethod = self::VARIANT_SELECTION_CHOICE;
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

        return $this;
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
        if (!in_array($variantSelectionMethod, array(self::VARIANT_SELECTION_CHOICE, self::VARIANT_SELECTION_MATCH))) {
            throw new \InvalidArgumentException(sprintf('Wrong variant selection method "%s" given.', $variantSelectionMethod));
        }

        $this->variantSelectionMethod = $variantSelectionMethod;

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTaxon(BaseTaxonInterface $taxon)
    {
        if (!$this->hasTaxon($taxon)) {
            $this->taxons->add($taxon);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxon(BaseTaxonInterface $taxon)
    {
        if ($this->hasTaxon($taxon)) {
            $this->taxons->removeElement($taxon);
        }

        return $this;
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
        return $this->getMasterVariant()->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->getMasterVariant()->setPrice($price);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory()
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(TaxCategoryInterface $category = null)
    {
        $this->taxCategory = $category;

        return $this;
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

        return $this;
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

        return $this;
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
    public static function getVariantSelectionMethodLabels()
    {
        return array(
            self::VARIANT_SELECTION_CHOICE => 'Variant choice',
            self::VARIANT_SELECTION_MATCH  => 'Options matching',
        );
    }

    public function __toString()
    {
        return $this->name;
    }
}
