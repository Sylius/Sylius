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

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippingMethodsAwareInterface
{
    /**
     * @return Collection|ShippingMethodInterface[]
     */
    public function getShippingMethods();

    /**
     * @param ShippingMethodInterface $shippingMethod
     *
     * @return bool
     */
    public function hasShippingMethod(ShippingMethodInterface $shippingMethod);

    /**
     * @param ShippingMethodInterface $shippingMethod
     */
    public function addShippingMethod(ShippingMethodInterface $shippingMethod);

    /**
     * @param ShippingMethodInterface $shippingMethod
     */
    public function removeShippingMethod(ShippingMethodInterface $shippingMethod);
}
