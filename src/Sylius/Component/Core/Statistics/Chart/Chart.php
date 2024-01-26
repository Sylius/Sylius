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

use Webmozart\Assert\Assert;

class Chart implements ChartInterface
{
    /**
     * @param array<string> $labels
     * @param array<string, array<array-key, int>> $datasets
     */
    public function __construct(private array $labels, private array $datasets)
    {
        $this->validateData($labels, $datasets);
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getDatasets(): array
    {
        return $this->datasets;
    }

    /**
     * @param array<string> $labels
     * @param array<string, array<array-key, int>> $datasets
     */
    private function validateData(array $labels, array $datasets): void
    {
        Assert::allString($labels);
        Assert::allIsArray($datasets);
        Assert::allString(array_keys($datasets));

        foreach ($datasets as $dataset) {
            Assert::allInteger($dataset);
            Assert::same(
                count($dataset),
                count($labels),
                'The number of elements in each dataset must match the number of labels.',
            );
        }
    }
}
