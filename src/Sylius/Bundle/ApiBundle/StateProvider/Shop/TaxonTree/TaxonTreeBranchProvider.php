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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonTreeBranchProvider extends AbstractTaxonTreeProvider
{
    public function getTreeResources(string $code, bool $includeRoot = false): Collection
    {
        $branchTaxons = array_filter(
            $this->taxonTreeProvider->getBranchWith($code, $includeRoot),
            static fn (TaxonInterface $taxon): bool => $taxon->isEnabled(),
        );

        return new ArrayCollection($branchTaxons);
    }
}
