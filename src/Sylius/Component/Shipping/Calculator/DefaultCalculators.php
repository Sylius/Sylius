<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DefaultCalculators
{
    /**
     * Flat rate per shipment calculator.
     */
    const FLAT_RATE = 'flat_rate';

    /**
     * Fixed price per unit calculator.
     */
    const PER_UNIT_RATE = 'per_unit_rate';

    /**
     * Flexible rate calculator.
     * Fixed price for first unit and constant rate
     * for each additional unit with a limit.
     */
    const FLEXIBLE_RATE = 'flexible_rate';

    /**
     * Fixed price per weight calculator.
     */
    const WEIGHT_RATE = 'weight_rate';

    /**
     * Flexible prices for weight ranges.
     */
    const WEIGHT_BUCKETS_RATE = 'weight_buckets_rate';
}
