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

namespace Sylius\Component\Core\Sales\Mapper;

use Sylius\Component\Core\Sales\ValueObject\SalesInPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;

interface SalesPeriodMapperInterface
{
    /**
     * @param array<array-key, array<array-key, mixed>> $ordersTotals
     *
     * @return SalesInPeriod[]
     */
    public function map(SalesPeriod $salesPeriod, array $ordersTotals): array;
}
