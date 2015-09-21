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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxonsAwareInterface
{
    /**
     * @param string $taxonomy
     *
     * @return Collection|TaxonInterface[]
     */
    public function getTaxons($taxonomy = null);

    /**
     * @param Collection $collection
     */
    public function setTaxons(Collection $collection);

    /**
     * @param TaxonInterface $taxon
     *
     * @return bool
     */
    public function hasTaxon(TaxonInterface $taxon);

    /**
     * @param TaxonInterface $taxon
     */
    public function addTaxon(TaxonInterface $taxon);

    /**
     * @param TaxonInterface $taxon
     */
    public function removeTaxon(TaxonInterface $taxon);
}
