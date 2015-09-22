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

/**
 * Stock location based calculator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocationBasedCalculator extends AbstractCalculator implements CalculatorInterface
{
    protected $parameterName = 'stockLocation';
    protected $className     = 'Sylius\Component\Core\Model\StockLocationInterface';

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::STOCK_LOCATION_BASED;
    }
}
