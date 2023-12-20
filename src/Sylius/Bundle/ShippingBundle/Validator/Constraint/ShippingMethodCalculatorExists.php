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

namespace Sylius\Bundle\ShippingBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ShippingMethodCalculatorExists extends Constraint
{
    public string $invalidShippingCalculator = 'sylius.shipping_method.calculator.invalid';

    public function validatedBy(): string
    {
        return 'sylius_shipping_method_calculator_exists';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
