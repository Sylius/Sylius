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

namespace Sylius\Bundle\ShippingBundle\Tests\Stub;

use Sylius\Bundle\ShippingBundle\Attribute\AsShippingCalculator;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

#[AsShippingCalculator(calculator: 'test', label: 'Test', formType: 'SomeFormType')]
final class ShippingCalculatorStub implements CalculatorInterface
{
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        return 0;
    }

    public function getType(): string
    {
        return '';
    }
}
