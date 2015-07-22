<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

/**
 * Shippable interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippableInterface
{
    /**
     * Get the weight of an item.
     *
     * @return int
     */
    public function getShippingWeight();

    /**
     * Get the volume of an item.
     *
     * @return int
     */
    public function getShippingVolume();

    /**
     * Get the width of an item.
     *
     * @return int
     */
    public function getShippingWidth();

    /**
     * Get the height of an item.
     *
     * @return int
     */
    public function getShippingHeight();

    /**
     * Get the depth of an item.
     *
     * @return int
     */
    public function getShippingDepth();

    /**
     * Get the shipping category for transportable object.
     *
     * @return ShippingCategoryInterface
     */
    public function getShippingCategory();
}
