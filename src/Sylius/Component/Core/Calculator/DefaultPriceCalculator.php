<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Model\PriceableInterface;

/**
 * Default calculator simply returns the priceable price.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class DefaultPriceCalculator implements PriceCalculatorInterface
{
    public function calculate(PriceableInterface $priceable)
    {
        return $priceable->getPrice();
    }
}
