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
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class DefaultCalculators
{
    /**
     * Flat rate per shipment calculator.
     */
    const FLAT_RATE           = 'flat_rate';

    /**
     * Fixed price per item calculator.
     */
    const PER_ITEM_RATE       = 'per_item_rate';

    /**
     * Flexible rate calculator.
     * Fixed price for first item and constant rate
     * for each additional item with a limit.
     */
    const FLEXIBLE_RATE       = 'flexible_rate';

    /**
     * Fixed price per weight calculator.
     */
    const WEIGHT_RATE         = 'weight_rate';

    /**
     * Flexible prices for weight ranges.
     */
    const WEIGHT_BUCKETS_RATE = 'weight_buckets_rate';
}
