<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Calculator;

use Sylius\Bundle\TaxationBundle\Model\TaxRateInterface;

/**
 * Default tax calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate($base, TaxRateInterface $rate)
    {
        if ($rate->isIncludedInPrice()) {
            return intval($base - round($base / (1 + $rate->getAmount())));
        }

        return intval(round($base * $rate->getAmount()));
    }
}
