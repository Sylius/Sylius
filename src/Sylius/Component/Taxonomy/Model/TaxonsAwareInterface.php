<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Taxons aware interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxonsAwareInterface
{
    /**
     * Get all taxons.
     *
     * @param string $taxonomy
     *
     * @return Collection|TaxonInterface[]
     */
    public function getTaxons($taxonomy = null);

    /**
     * Set the taxons.
     *
     * @param Collection $collection
     */
    public function setTaxons(Collection $collection);

    /**
     * Has a taxon?
     *
     * @param TaxonInterface $taxon
     *
     * @return Boolean
     */
    public function hasTaxon(TaxonInterface $taxon);

    /**
     * Add taxon.
     *
     * @param TaxonInterface $taxon
     */
    public function addTaxon(TaxonInterface $taxon);

    /**
     * Remove taxon.
     *
     * @param TaxonInterface $taxon
     */
    public function removeTaxon(TaxonInterface $taxon);
}
