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
use Sylius\Component\Core\Pricing\Calculators;
use Sylius\Component\Product\Model\Variant as BaseVariant;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;

/**
 * Sylius core product variant entity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariant extends BaseVariant implements ProductVariantInterface
{
    /**
     * Variant SKU.
     *
     * @var string
     */
    protected $sku;

    /**
     * The variant price.
     *
     * @var int
     */
    protected $price;

    /**
     * @var int
     */
    protected $originalPrice;

    /**
     * The pricing calculator.
     *
     * @var string
     */
    protected $pricingCalculator = Calculators::STANDARD;

    /**
     * The pricing configuration.
     *
     * @var array
     */
    protected $pricingConfiguration = array();

    /**
     * On hold.
     *
     * @var int
     */
    protected $onHold = 0;

    /**
     * On hand stock.
     *
     * @var int
     */
    protected $onHand = 0;

    /**
     * Sold amount.
     *
     * @var int
     */
    protected $sold = 0;

    /**
     * Is variant available on demand?
     *
     * @var bool
     */
    protected $availableOnDemand = true;

    /**
     * Images.
     *
     * @var Collection|ProductVariantImageInterface[]
     */
    protected $images;

    /**
     * Weight.
     *
     * @var float
     */
    protected $weight;

    /**
     * Width.
     *
     * @var float
     */
    protected $width;

    /**
     * Height.
     *
     * @var float
     */
    protected $height;

    /**
     * Depth.
     *
     * @var float
     */
    protected $depth;

    /**
     * Override constructor to set on hand stock.
     */
    public function __construct()
    {
        parent::__construct();

        $this->images = new ArrayCollection();
    }

    public function __toString()
    {
        $string = $this->getProduct()->getName();

        if (!$this->getOptions()->isEmpty()) {
            $string .= '(';

            foreach ($this->getOptions() as $option) {
                $string .= $option->getOption()->getName() . ': ' . $option->getValue() . ', ';
            }

            $string = substr($string, 0, -2) . ')';
        }

        return $string;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMetadataClassIdentifier()
    {
        return 'ProductVariant';
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataIdentifier()
    {
        return $this->getMetadataClassIdentifier() . '-' . $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
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
        if (!is_int($price)) {
            throw new \InvalidArgumentException('Price must be an integer.');
        }
        $this->price = $price;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalPrice($originalPrice)
    {
        if (!is_int($originalPrice)) {
            throw new \InvalidArgumentException('Original price must be an integer.');
        }
        $this->originalPrice = $originalPrice;
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
    public function getPricingCalculator()
    {
        return $this->pricingCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function setPricingCalculator($calculator)
    {
        $this->pricingCalculator = $calculator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPricingConfiguration()
    {
        return $this->pricingConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setPricingConfiguration(array $configuration)
    {
        $this->pricingConfiguration = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock()
    {
        return 0 < $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHold()
    {
        return $this->onHold;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand()
    {
        return $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand($onHand)
    {
        $this->onHand = $onHand;

        if (0 > $this->onHand) {
            $this->onHand = 0;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSold()
    {
        return $this->sold;
    }

    /**
     * {@inheritdoc}
     */
    public function setSold($sold)
    {
        $this->sold = (int)$sold;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableOnDemand()
    {
        return $this->availableOnDemand;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableOnDemand($availableOnDemand)
    {
        $this->availableOnDemand = (bool)$availableOnDemand;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaults(BaseVariantInterface $masterVariant)
    {
        parent::setDefaults($masterVariant);

        $this->setPrice($masterVariant->getPrice());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCategory()
    {
        return $this->getProduct()->getShippingCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(ProductVariantImageInterface $image)
    {
        return $this->images->contains($image);
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
    public function getImage()
    {
        if ($this->images->isEmpty()) {
            return $this->getProduct()->getImage();
        }

        return $this->images->first();
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ProductVariantImageInterface $image)
    {
        if (!$this->hasImage($image)) {
            $image->setVariant($this);
            $this->images->add($image);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(ProductVariantImageInterface $image)
    {
        $image->setVariant(null);
        $this->images->removeElement($image);

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingWeight()
    {
        return $this->getWeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingWidth()
    {
        return $this->getWidth();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingHeight()
    {
        return $this->getHeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingDepth()
    {
        return $this->getDepth();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingVolume()
    {
        return $this->depth * $this->height * $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function isPriceReduced()
    {
        return $this->originalPrice > $this->price;
    }
}
