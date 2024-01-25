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

interface ChartFactoryInterface
{
    /** @param array<array{period: \DateTimeInterface, total: int}> $datasets */
    public function createTimeSeries(string $intervalType, string $datasetName, array $datasets): ChartInterface;
}
