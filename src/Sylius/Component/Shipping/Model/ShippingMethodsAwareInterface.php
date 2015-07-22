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
 * Interface for object referencing multiple shipping methods.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
     * @return Boolean
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
