<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Pricing;

use Sylius\Component\Pricing\Calculator\Calculators as BaseCalculators;

/**
 * Core calculators.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Calculators extends BaseCalculators
{
    // Group based pricing.
    const GROUP_BASED = 'group_based';

    // Address zone based pricing.
    const ZONE_BASED = 'zone_based';
}
