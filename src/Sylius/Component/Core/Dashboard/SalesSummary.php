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

namespace Sylius\Component\Core\Dashboard;

trigger_deprecation(
    'sylius/core',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    SalesSummary::class,
);

/**
 * @experimental
 */
final class SalesSummary implements SalesSummaryInterface
{
    public function __construct(
        /** @var array<string, string> */
        private array $intervalsSalesMap,
    ) {
    }

    public function getIntervals(): array
    {
        return array_keys($this->intervalsSalesMap);
    }

    public function getSales(): array
    {
        return array_values($this->intervalsSalesMap);
    }
}
