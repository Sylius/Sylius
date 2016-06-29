<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Pricing;

use Sylius\Pricing\Calculator\Calculators as BaseCalculators;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Calculators extends BaseCalculators
{
    const CHANNEL_BASED = 'channel_based';
    const GROUP_BASED = 'group_based';
    const ZONE_BASED = 'zone_based';
}
