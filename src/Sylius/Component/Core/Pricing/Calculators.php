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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Calculators extends BaseCalculators
{
    const CHANNEL_BASED = 'channel_based';
    const CHANNEL_AND_CURRENCY_BASED = 'channel_and_currency_based';
    const GROUP_BASED = 'group_based';
    const ZONE_BASED = 'zone_based';
}
