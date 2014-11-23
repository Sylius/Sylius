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
use Sylius\Component\Inventory\Model\StockItem;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
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
    protected $pricingConfiguration = array ();

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
     * Stock Items
     *
     * @var StockItemInterface[]|Collection
     */
    protected $items;

    /**
     * Override constructor to set on hand stock.
     */
    public function __construct()
    {
        parent::__construct();

        $this->images = new ArrayCollection();
        $this->stockItems = new ArrayCollection();
    }

    public function __toString()
    {
        $string = $this->getProduct()->getName();
        if (!$this->getOptions()->isEmpty()) {
            $string .= ' (' . $this->getOptionsString() . ')';
        }

        return $string;
    }

    /**
     * Get the options in string format
     * @return string
     */
    public function getOptionsString()
    {
        $string = "";
        if (!$this->getOptions()->isEmpty()) {
            foreach ($this->getOptions() as $option) {
                $string .= $option->getOption()->getName() . ': ' . $option->getValue() . ', ';
            }
            $string = substr($string, 0, -2);
        }

        return $string;
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
        $this->price = (int) $price;

        return $this;
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
        foreach ($this->items as $item) {
            if ($item->getOnHand() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sold amount.
     *
     * @var int
     */
    protected $sold = 0;

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
        $this->sold = (int) $sold;

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
    public function getStockItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function addStockItem(StockItemInterface $stockItem)
    {
        $stockItem->setStockable($this);

        $this->items->add($stockItem);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeStockItem(StockItemInterface $stockItem)
    {
        $this->items->removeElement($stockItem);

        return $this;
    }

    /**
     * @param StockLocationInterface $location
     *
     * @return StockItemInterface
     */
    public function getStockItemForLocation(StockLocationInterface $location)
    {
        foreach ($this->items as $item) {
            if ($item->getStockLocation() === $location) {
                return $item;
            }
        }


        //TODO, Remove this from the model and create stockItems for every location elsewere
        $item = new StockItem();
        $item->setStockLocation($location);

        $this->addStockItem($item);

        return $item;
    }
}
