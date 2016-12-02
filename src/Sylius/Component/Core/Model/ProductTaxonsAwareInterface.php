<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface ProductTaxonsAwareInterface
{
    /**
     * @return Collection|ProductTaxonInterface[]
     */
    public function getProductTaxons();

    /**
     * @param ProductTaxonInterface $productTaxon
     *
     * @return bool
     */
    public function hasProductTaxon(ProductTaxonInterface $productTaxon);

    /**
     * @param ProductTaxonInterface $productTaxon
     */
    public function addProductTaxon(ProductTaxonInterface $productTaxon);

    /**
     * @param ProductTaxonInterface $productTaxon
     */
    public function removeProductTaxon(ProductTaxonInterface $productTaxon);

    /**
     * @param TaxonInterface $taxon
     *
     * @return ProductTaxonInterface|null
     */
    public function filterProductTaxonsByTaxon(TaxonInterface $taxon);
}
