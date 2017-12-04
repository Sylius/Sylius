<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShipmentInterface;

final class PerUnitRateCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        return (int) ($configuration['amount'] * $subject->getShippingUnitCount());
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'per_unit_rate';
    }
}
