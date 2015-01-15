<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Finder;

use Sylius\Bundle\SearchBundle\Query\Query;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;

/**
 * Finder
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
interface FinderInterface
{
    /**
     * @param TaxonQuery $query
     *
     * @return mixed
     */
    public function getResultsForTaxon(TaxonQuery $query);

    /**
     * @param SearchStringQuery $query
     *
     * @return mixed
     */
    public function getResults(SearchStringQuery $query);

    /**
     * @param Query $queryObject
     *
     * @return mixed
     */
    public function find(Query $queryObject);
}
