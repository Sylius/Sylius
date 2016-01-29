<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Pricing\Calculator;

/**
 * Default calculators.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Calculators
{
    // Standard pricing.
    const STANDARD = 'standard';

    // Time based discount.
    const TIME_BASED = 'time_based';

    // Volume based pricing.
    const VOLUME_BASED = 'volume_based';
}
