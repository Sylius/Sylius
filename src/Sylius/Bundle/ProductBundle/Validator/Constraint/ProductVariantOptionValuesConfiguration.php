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

namespace Sylius\Bundle\ProductBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ProductVariantOptionValuesConfiguration extends Constraint
{
    public string $message = 'sylius.product_variant.option_values.not_configured';

    public function validatedBy(): string
    {
        return 'sylius.validator.product_variant_option_values_configuration';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
