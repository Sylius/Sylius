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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class OptionsBasedVariantSelectionMethod extends Constraint
{
    public string $message = 'sylius.product.variant_selection_method.options_based_with_no_options';

    public function validatedBy(): string
    {
        return 'sylius_options_based_variant_selection_method';
    }

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
