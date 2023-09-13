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

namespace Sylius\Bundle\TaxationBundle\Tests\Stub;

use Sylius\Bundle\TaxationBundle\Attribute\AsTaxCalculator;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

#[AsTaxCalculator(calculator: 'test')]
final class TaxCalculatorStub implements CalculatorInterface
{
    public function calculate(float $base, TaxRateInterface $rate): float
    {
        return 0.0;
    }
}
