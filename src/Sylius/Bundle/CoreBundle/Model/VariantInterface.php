<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\VariableProductBundle\Model\VariantInterface as BaseVariantInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippableInterface;

/**
 * Sylius core product Variant interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface VariantInterface extends BaseVariantInterface, ShippableInterface, StockableInterface
{
    /**
     * Get variant price.
     *
     * @return integer
     */
    public function getPrice();

    /**
     * Set the price.
     *
     * @param integer $price
     */
    public function setPrice($price);

    /**
     * Get images.
     *
     * @return VariantImageInterface[]
     */
    public function getImages();

    /**
     * Checks if product has image.
     *
     * @param VariantImageInterface $image
     *
     * @return Boolean
     */
    public function hasImage(VariantImageInterface $image);

    /**
     * Add image.
     *
     * @param VariantImageInterface $image
     */
    public function addImage(VariantImageInterface $image);

    /**
     * Remove image.
     *
     * @param VariantImageInterface $image
     */
    public function removeImage(VariantImageInterface $image);

    /**
     * @return integer
     */
    public function getWeight();
    public function setWeight($weight);

    public function getWidth();
    public function setWidth($width);

    public function getHeight();
    public function setHeight($height);

    public function getDepth();
    public function setDepth($depth);
}
