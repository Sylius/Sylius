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

namespace Sylius\Component\Taxonomy\Provider;

use Sylius\Component\Taxonomy\Exception\TaxonNotFoundException;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonTreeProviderInterface
{
    /**
     * Returns a branch of the tree containing the taxon with given code,
     * includes the path up to the top and all children.
     * Using the code of a root taxon will return the whole tree.
     *
     * @return TaxonInterface[]
     *
     * @throws TaxonNotFoundException
     */
    public function getBranchWith(string $taxonCode, bool $includeRoot = false): array;

    /**
     * Returns the path to the taxon with given code,
     * starting from the top and ending with the taxon itself.
     *
     * @return TaxonInterface[]
     *
     * @throws TaxonNotFoundException
     */
    public function getPathTo(string $taxonCode, bool $includeRoot = false): array;
}
