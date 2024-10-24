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

namespace Sylius\Bundle\TaxonomyBundle\Repository;

interface TaxonTreeRepositoryInterface
{
    /**
     * @param string|string[]|null $sortByField
     * @param string|string[] $direction
     *
     * @return array<object>|null
     */
    public function children(
        ?object $node = null,
        bool $direct = false,
        array|string|null $sortByField = null,
        array|string $direction = 'ASC',
        bool $includeNode = false,
    ): array|null;
}
