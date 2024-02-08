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

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class InStock extends Constraint
{
    public string $message = 'sylius.cart_item.not_available';

    public string $stockablePath = 'stockable';

    public string $quantityPath = 'quantity';

    public function validatedBy(): string
    {
        return 'sylius_in_stock';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
