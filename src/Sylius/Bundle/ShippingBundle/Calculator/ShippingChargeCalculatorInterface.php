<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Calculator;

use Sylius\Bundle\ShippingBundle\Model\ShippableInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Shipping charge calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface ShippingChargeCalculatorInterface
{
    /**
     * Get the shipping charge for given method, shippable and additional
     * context.
     *
     * @param ShippingMethodInterface $method
     * @param ShippableInterface      $shippable
     * @param array                   $context
     *
     * @return mixed
     */
    public function calculate(ShippingMethodInterface $method, ShippableInterface $shippable, array $context = array());
}
