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

    private function __construct()
    {
    }
}
