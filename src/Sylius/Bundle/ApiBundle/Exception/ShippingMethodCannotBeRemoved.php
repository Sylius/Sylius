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

namespace Sylius\Bundle\ApiBundle\Exception;

/** @experimental */
final class ShippingMethodCannotBeRemoved extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cannot delete, the shipping method is in use.');
    }
}
