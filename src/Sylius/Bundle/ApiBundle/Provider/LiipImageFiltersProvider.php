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

namespace Sylius\Bundle\ApiBundle\Provider;

class LiipImageFiltersProvider implements ImageFiltersProviderInterface
{
    /** @var array<string> */
    private array $filters;

    /** @param array<string, mixed> $filters */
    public function __construct(array $filters)
    {
        $this->filters = array_keys($filters);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
