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
use Symfony\Component\Validator\Constraints\Range;

final class CartItemQuantityRange extends Range
{
    public function validatedBy(): string
    {
        return 'sylius_cart_item_quantity_range';
    }

    public function getTargets(): string
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}
