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

namespace Sylius\Component\Core\Statistics\Chart;

use Sylius\Component\Core\DateTime\Period;

interface ChartFactoryInterface
{
    /**
     * @param array<array-key, array<string, object>> $namedDatasets
     */
    public function createTimeSeries(Period $period, array $namedDatasets): ChartInterface;
}
