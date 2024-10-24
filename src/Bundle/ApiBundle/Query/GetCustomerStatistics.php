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

final class GetCustomerStatistics
{
    public function __construct(
        private int $customerId,
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }
}
