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

namespace Sylius\Component\Core\Promotion\Filter;

use Webmozart\Assert\Assert;

final class CompositeFilter implements FilterInterface
{
    /**
     * @param FilterInterface[] $filters
     */
    public function __construct(
        private iterable $filters,
    ) {
        Assert::allIsInstanceOf($this->filters, FilterInterface::class);
        Assert::notEmpty($filters);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function filter(array $items, array $configuration): array
    {
        foreach ($this->filters as $filter) {
            $items = $filter->filter($items, $configuration);
        }

        return $items;
    }
}
