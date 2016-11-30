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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class DefaultCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate($base, TaxRateInterface $rate)
    {
        if ($rate->isIncludedInPrice()) {
            return (int) round($base - ($base / (1 + $rate->getAmount())));
        }

        return (int) round($base * $rate->getAmount());
    }
}
