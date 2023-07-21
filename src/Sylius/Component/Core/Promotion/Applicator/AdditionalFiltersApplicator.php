<?php

/*
 * This file is part of Sylius corporate website.
 *
 * (c) Sylius <sylius+sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Promotion\Applicator;

use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Webmozart\Assert\Assert;

final class AdditionalFiltersApplicator implements AdditionalFiltersApplicatorInterface
{
    /**
     * @param FilterInterface[] $additionalItemFilters
     */
    public function __construct(
        private iterable $additionalItemFilters,
    ) {
        Assert::allIsInstanceOf($this->additionalItemFilters, FilterInterface::class);
    }

    public function apply(array $filteredItems, array $configuration): array
    {
        foreach($this->additionalItemFilters as $additionalItemFilter) {
            $filteredItems = $additionalItemFilter->filter($filteredItems, $configuration);
        }

        return $filteredItems;
    }
}
