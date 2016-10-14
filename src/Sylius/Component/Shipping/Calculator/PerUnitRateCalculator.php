<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PerUnitRateCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject, array $configuration)
    {
        return (int) ($configuration['amount'] * $subject->getShippingUnitCount());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'per_unit_rate';
    }
}
