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

/**
 * Default calculators.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
final class DefaultCalculators
{
    const FLAT_RATE     = 'flat_rate';
    const PER_ITEM_RATE = 'per_item_rate';
    const FLEXIBLE_RATE = 'flexible_rate';
}
