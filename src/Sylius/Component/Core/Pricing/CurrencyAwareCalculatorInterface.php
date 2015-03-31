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

use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface CurrencyAwareCalculatorInterface extends CalculatorInterface
{
    /**
     * Determine if calculator output must skip currency conversion or not
     *
     * @return boolean
     */
    public function isCurrencySpecific();
}
