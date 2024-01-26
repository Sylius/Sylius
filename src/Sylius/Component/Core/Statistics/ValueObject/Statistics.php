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

use Sylius\Component\Core\Statistics\Chart\ChartInterface;

class Statistics
{
    public function __construct(
        private ChartInterface $chart,
        private BusinessActivitySummary $businessActivitySummary,
    ) {
    }

    public function getSalesChart(): ChartInterface
    {
        return $this->chart;
    }

    public function getBusinessActivitySummary(): BusinessActivitySummary
    {
        return $this->businessActivitySummary;
    }
}
