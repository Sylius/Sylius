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

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\VariableProductBundle\Model\VariantInterface as BaseVariantInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;

/**
 * Sylius core product Variant interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface VariantInterface extends BaseVariantInterface, PriceableInterface, ShippableInterface, StockableInterface
{
    /**
     * Get images.
     *
     * @return Collection|VariantImageInterface[]
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

    /**
     * @param integer $weight
     */
    public function setWeight($weight);

    /**
     * @return integer
     */
    public function getWidth();

    /**
     * @param integer $width
     */
    public function setWidth($width);

    /**
     * @return integer
     */
    public function getHeight();

    /**
     * @param integer $height
     */
    public function setHeight($height);

    /**
     * @return integer
     */
    public function getDepth();

    /**
     * @param integer $depth
     */
    public function setDepth($depth);
}
