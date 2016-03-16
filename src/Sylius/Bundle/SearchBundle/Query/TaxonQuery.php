<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Query;

use Sylius\Component\Taxonomy\Model\TaxonInterface;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class TaxonQuery extends Query
{
    /**
     * @var TaxonInterface
     */
    protected $taxon;

    /**
     * @param TaxonInterface $taxon
     * @param mixed          $appliedFilters
     */
    public function __construct(TaxonInterface $taxon, $appliedFilters = [])
    {
        $this->appliedFilters = (array) $appliedFilters;
        $this->taxon = $taxon;
    }

    /**
     * @param TaxonInterface $taxon
     */
    public function setTaxon(TaxonInterface $taxon)
    {
        $this->taxon = $taxon;
    }

    /**
     * @return TaxonInterface
     */
    public function getTaxon()
    {
        return $this->taxon;
    }
}
