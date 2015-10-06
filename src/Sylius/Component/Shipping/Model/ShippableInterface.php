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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippableInterface
{
    /**
     * @return int
     */
    public function getShippingWeight();

    /**
     * @return int
     */
    public function getShippingVolume();

    /**
     * @return int
     */
    public function getShippingWidth();

    /**
     * @return int
     */
    public function getShippingHeight();

    /**
     * @return int
     */
    public function getShippingDepth();

    /**
     * @return ShippingCategoryInterface
     */
    public function getShippingCategory();
}
