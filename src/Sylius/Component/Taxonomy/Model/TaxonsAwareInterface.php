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

namespace Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\Collection;

interface TaxonsAwareInterface
{
    /**
     * @return Collection<array-key, TaxonInterface>
     */
    public function getTaxons(): Collection;

    public function hasTaxon(TaxonInterface $taxon): bool;

    public function addTaxon(TaxonInterface $taxon): void;

    public function removeTaxon(TaxonInterface $taxon): void;
}
