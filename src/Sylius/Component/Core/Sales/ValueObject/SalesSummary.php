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

namespace Sylius\Component\Core\Sales\ValueObject;

class SalesSummary
{
    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private int $totalSales,
        private int $newOrdersCount,
        private int $newCustomersCount,
    ) {
    }

    public function getTotalSales(): int
    {
        return $this->totalSales;
    }

    public function getNewOrdersCount(): int
    {
        return $this->newOrdersCount;
    }

    public function getNewCustomersCount(): int
    {
        return $this->newCustomersCount;
    }

    public function getAverageOrderValue(): int
    {
        if (0 === $this->newOrdersCount) {
            return 0;
        }

        return (int) round($this->totalSales / $this->newOrdersCount);
    }
}
