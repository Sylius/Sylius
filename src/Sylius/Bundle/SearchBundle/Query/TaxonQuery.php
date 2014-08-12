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
use Sylius\Component\Core\Model\Taxon;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class TaxonQuery extends Query
{

    /* @var */
    private $taxon;

    /**
     * @param Taxon $taxon
     * @param       $appliedFilters
     */
    public function __construct(Taxon $taxon, $appliedFilters)
    {
        parent::setAppliedFilters($appliedFilters);
        $this->taxon = $taxon;
    }

    /**
     * @param Taxon $taxon
     */
    public function setTaxon(Taxon $taxon)
    {
        $this->taxon = $taxon;
    }

    /**
     * @return Taxon
     */
    public function getTaxon()
    {
        return $this->taxon;
    }

} 