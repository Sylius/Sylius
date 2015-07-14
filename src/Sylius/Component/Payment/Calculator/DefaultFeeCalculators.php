<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Calculator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultFeeCalculators
{
    /**
     * Fixed fee calculator for payment
     */
    const FIXED = 'fixed';

    /**
     * Percent fee calculator for payment
     */
    const PERCENT = 'percent';
}
