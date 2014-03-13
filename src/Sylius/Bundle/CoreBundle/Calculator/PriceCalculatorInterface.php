<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Calculator;

use Sylius\Bundle\CoreBundle\Model\VariantInterface;

/**
 * Allows flexible price calculations.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PriceCalculatorInterface
{
    public function calculate(VariantInterface $variant);
}
