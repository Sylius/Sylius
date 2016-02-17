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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;

/**
 * Address zone based calculator.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ZoneBasedCalculator extends AbstractCalculator implements CalculatorInterface
{
    protected $parameterName = 'zones';
    protected $className = ZoneInterface::class;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::ZONE_BASED;
    }
}
