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

namespace Sylius\Component\Core\Inventory\Exception;

final class NotEnoughUnitsOnHoldException extends \RuntimeException
{
    public function __construct(string $variantName)
    {
        parent::__construct(sprintf(
            'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
            $variantName,
        ));
    }
}
