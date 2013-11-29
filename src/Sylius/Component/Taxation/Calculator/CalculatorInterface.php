<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Calculator;

use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * Tax calculator interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CalculatorInterface
{
    /**
     * Get the tax amount for given base amount and tax rate.
     *
     * @param float            $base
     * @param TaxRateInterface $rate
     *
     * @return float
     */
    public function calculate($base, TaxRateInterface $rate);
}
