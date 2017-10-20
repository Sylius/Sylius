<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;

interface ProductTaxonsAwareInterface
{
    /**
     * @return Collection|ProductTaxonInterface[]
     */
    public function getProductTaxons(): Collection;

    /**
     * @param ProductTaxonInterface $productTaxon
     *
     * @return bool
     */
    public function hasProductTaxon(ProductTaxonInterface $productTaxon): bool;

    /**
     * @param ProductTaxonInterface $productTaxon
     */
    public function addProductTaxon(ProductTaxonInterface $productTaxon): void;

    /**
     * @param ProductTaxonInterface $productTaxon
     */
    public function removeProductTaxon(ProductTaxonInterface $productTaxon): void;

    /**
     * @return Collection|TaxonInterface[]
     */
    public function getTaxons(): Collection;

    /**
     * @param TaxonInterface $taxon
     *
     * @return bool
     */
    public function hasTaxon(TaxonInterface $taxon): bool;
}
