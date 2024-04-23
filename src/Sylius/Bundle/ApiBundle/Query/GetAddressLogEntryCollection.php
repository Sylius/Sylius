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

namespace Sylius\Bundle\ApiBundle\Query;

final class GetAddressLogEntryCollection
{
    public function __construct(
        private int $addressId,
    ) {
    }

    public function getAddressId(): int
    {
        return $this->addressId;
    }
}
