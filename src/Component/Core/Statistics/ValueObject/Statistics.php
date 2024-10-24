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

namespace Sylius\Component\Core\Statistics\ValueObject;

class Statistics
{
    /** @param array<array{total: int, period: string}> $sales */
    public function __construct(
        private array $sales,
        private BusinessActivitySummary $businessActivitySummary,
    ) {
    }

    /** @return array<array{total: int, period: string}> */
    public function getSales(): array
    {
        return $this->sales;
    }

    public function getBusinessActivitySummary(): BusinessActivitySummary
    {
        return $this->businessActivitySummary;
    }
}
