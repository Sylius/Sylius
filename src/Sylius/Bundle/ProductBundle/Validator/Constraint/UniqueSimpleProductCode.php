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

final class UniqueSimpleProductCode extends Constraint
{
    public string $message = 'sylius.simple_product.code.unique';

    public function validatedBy(): string
    {
        return 'sylius.validator.unique_simple_product_code';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
