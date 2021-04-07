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

namespace Sylius\Bundle\ProductBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ProductVariantOptionsChecker extends Constraint
{
    /** @var string */
    public $nullMessage = 'sylius.option.not_null';

    /** @var string */
    public $emptyMessage = 'sylius.option.not_empty';

    public function validatedBy(): string
    {
        return 'sylius.validator.product_variant_options_checker';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
