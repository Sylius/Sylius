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

#[\Attribute]
final class ValidDeliveryTimeRange extends Constraint
{
    public string $message = 'sylius.form.shipping_method.validation.max_delivery_time_days_greater_or_equal_min';

    public function validatedBy(): string
    {
        return 'sylius_shipping_method_valid_delivery_time_range';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
