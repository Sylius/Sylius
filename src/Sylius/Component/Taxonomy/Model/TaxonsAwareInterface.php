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
     * @return Collection|TaxonInterface[]
     */
    public function getTaxons();

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
