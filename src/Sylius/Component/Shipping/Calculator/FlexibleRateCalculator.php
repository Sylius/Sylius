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
class FlexibleRateCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject, array $configuration)
    {
        $firstUnitCost = $configuration['first_unit_cost'];
        $additionalUnitCost = $configuration['additional_unit_cost'];
        $additionalUnitLimit = $configuration['additional_unit_limit'];

        $totalUnits = $subject->getShippingUnitCount();
        $additionalUnits = $totalUnits - 1;

        if (0 !== $additionalUnitLimit) {
            $additionalUnits = $additionalUnitLimit >= $additionalUnits ? $additionalUnits : $additionalUnitLimit;
        }

        return (int) ($firstUnitCost + ($additionalUnits * $additionalUnitCost));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'flexible_rate';
    }
}
