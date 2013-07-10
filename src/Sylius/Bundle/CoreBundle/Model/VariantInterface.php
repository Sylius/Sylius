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

use Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface as BaseVariantInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;
use Sylius\Bundle\SalesBundle\Model\SellableInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippableInterface;

/**
 * Sylius core product Variant interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface VariantInterface extends
    BaseVariantInterface,
    ShippableInterface,
    StockableInterface,
    SellableInterface
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
     * @return Collection
     */
    public function getImages();

    /**
     * Checks if product has image.
     *
     * @return Boolean
     */
    public function hasImage(VariantImageInterface $image);

    /**
     * Add image.
     *
     * @param VariantImage $image
     */
    public function addImage(VariantImageInterface $image);

    /**
     * Remove image.
     *
     * @param VariantImage $image
     */
    public function removeImage(VariantImageInterface $image);
}
