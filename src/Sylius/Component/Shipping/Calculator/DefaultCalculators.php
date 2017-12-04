<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Shipping\Calculator;

final class DefaultCalculators
{
    /**
     * Flat rate per shipment calculator.
     */
    public const FLAT_RATE = 'flat_rate';

    /**
     * Fixed price per unit calculator.
     */
    public const PER_UNIT_RATE = 'per_unit_rate';

    private function __construct()
    {
    }
}
