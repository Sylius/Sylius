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

class LiipProductImageFilterProvider implements ProductImageFilterProviderInterface
{
    private array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function provideAllFilters(): array
    {
        return $this->filters;
    }

    public function provideShopFilters(): array
    {
        $filters = $this->provideAllFilters();

        return $this->removeAdminFilters($filters);
    }

    private function removeAdminFilters(array $filters): array
    {
        /** @var string $filter */
        foreach ($filters as $key => $filter) {
            if (str_contains($key, 'admin')) {
                unset($filters[$key]);
            }
        }

        return $filters;
    }
}
