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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class PlacedOrderCartItemsImmutable extends Constraint
{
    public string $message = 'sylius.order.cart_items_immutable';

    public function validatedBy(): string
    {
        return 'sylius_validator_placed_order_cart_items_immutable';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
