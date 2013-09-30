<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Sorter;


use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Applies a sort order to an array of ShippingMethod objects
 */
interface ShippingMethodSorterInterface
{
    /**
     * Sorts shipping methods
     *
     * @param ShippingMethodInterface[] $methods
     * @param ShippingSubjectInterface $subject
     * @return ShippingMethodInterface[]
     */
    function sort(array $methods, ShippingSubjectInterface $subject);
}