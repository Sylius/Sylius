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

namespace Sylius\Bundle\ShippingBundle\Application\Calculator;

use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Calculator\SettableTypeCalculatorInterface;
use Sylius\Component\Shipping\Calculator\SettableTypeCalculatorTrait;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class Calculator implements CalculatorInterface, SettableTypeCalculatorInterface
{
    use SettableTypeCalculatorTrait;

    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        return 10;
    }
}
