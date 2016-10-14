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
    const FLAT_RATE = 'flat_rate';
    const PER_UNIT_RATE = 'per_unit_rate';
    const FLEXIBLE_RATE = 'flexible_rate';
    const WEIGHT_RATE = 'weight_rate';
    const WEIGHT_BUCKETS_RATE = 'weight_buckets_rate';

    private function __construct()
    {
    }
}
