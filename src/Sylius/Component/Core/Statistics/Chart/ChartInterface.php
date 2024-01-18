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

interface ChartInterface
{
    /** @return array<string> */
    public function getLabels(): array;

    /**
     * Array of named datasets
     *
     * @return array<string, array<array-key, int>>
     */
    public function getDatasets(): array;
}
