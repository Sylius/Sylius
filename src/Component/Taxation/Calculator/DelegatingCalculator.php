<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Taxation\Calculator;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class DelegatingCalculator implements CalculatorInterface
{
    public function __construct(private ServiceRegistryInterface $calculatorsRegistry)
    {
    }

    public function calculate(float $base, TaxRateInterface $rate): float
    {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorsRegistry->get($rate->getCalculator());

        return $calculator->calculate($base, $rate);
    }
}
