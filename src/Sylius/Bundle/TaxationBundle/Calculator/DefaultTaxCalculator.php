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
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class DefaultTaxCalculator implements TaxCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate($base, TaxRateInterface $rate)
    {
        return round(bcmul($base, $rate->getAmount(), 2), 2);
    }
}
