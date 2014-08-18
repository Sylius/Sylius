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

use Doctrine\ORM\EntityManager;
use Sylius\Bundle\SearchBundle\Query\Query;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * OrmFinder
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class OrmFinder implements FinderInterface
{
    /**
     * @var
     */
    private $searchRepository;

    /**
     * @var
     */
    private $config;

    /**
     * @var
     */
    private $productRepository;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var
     */
    private $queryLogger;

    /**
     * @var
     */
    private $facets;

    /**
     * @var
     */
    private $filters;

    /**
     * @var
     */
    private $paginator;

    /**
     * @var
     */
    private $facetGroup;

    /**
     * @var
     */
    private $targetIndex;

    /**
     * @param                      $searchRepository
     * @param                      $config
     * @param                      $productRepository
     * @param EntityManager        $em
     * @param QueryLoggerInterface $queryLogger
     */
    public function __construct($searchRepository, $config, $productRepository, EntityManager $em, QueryLoggerInterface $queryLogger)
    {
        $this->searchRepository  = $searchRepository;
        $this->config            = $config;
        $this->productRepository = $productRepository;
        $this->em                = $em;
        $this->queryLogger = $queryLogger;
    }

    /**
     * @return mixed
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return mixed
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param $targetIndex
     *
     * @return $this
     */
    public function setTargetIndex($targetIndex)
    {
        $this->targetIndex = $targetIndex;

        return $this;
    }

    /**
     * @param $facetGroup
     *
     * @return $this
     */
    public function setFacetGroup($facetGroup)
    {
        $this->facetGroup = $facetGroup;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * TODO: a simple if does the job for now, in future this should move to a
     * chain of responsibility pattern or something similar.
     */
    public function find(Query $queryObject)
    {
        if ($queryObject instanceof SearchStringQuery) {

            if ($this->queryLogger->isEnabled()) {
                $this->queryLogger->logStringQuery(
                    $queryObject->getSearchTerm(),
                    $queryObject->getRemoteAddress()
                );
            }

            return $this->getResults($queryObject);
        }

        if ($queryObject instanceof TaxonQuery) {
            return $this->getResultsForTaxon($queryObject);
        }

        throw new Exception("finder can't handle this currently, feel free to implement it!");
    }

    /**
     * {@inheritdoc}
     */
    public function getResultsForTaxon(TaxonQuery $query)
    {
        if (isset($this->facetGroup)) {
            $this->getConfiguredFilterSetsForFinders($this->facetGroup);
        }

        $paginator = $this->productRepository->createByTaxonPaginator($query->getTaxon(), array());

        // calculates the facets of the result set
        $ids = array();
        foreach ($paginator as $product) {
            $ids[] = $product->getId();
        }

        $result       = array();
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u.itemId, u.tags')
            ->from('Sylius\Bundle\SearchBundle\Entity\SearchIndex', 'u')
            ->where('u.itemId IN (:ids)')
            ->setParameter('ids', $ids);

        $res = $queryBuilder->getQuery()->getResult();
        foreach ($res as $facet) {
            $result[$facet['itemId']] = $facet['tags'];
        }

        $appliedFilters = $query->getAppliedFilters();
        if (!$appliedFilters) {
            $appliedFilters = array();
        }

        list($facetFilteredIds, $idsFromAllFacets) = $this->getFilteredIds($appliedFilters, $ids);

        $facets = null;
        if (isset($this->facetGroup)) {
            $facets = $this->calculateNewFacets($result, $facetFilteredIds);
        }

        $paginator = $this->productRepository->createByTaxonPaginator($query->getTaxon(), array('id' => $idsFromAllFacets));

        $this->facets = $facets;
        $this->paginator = $paginator;
        $this->filters = $query->getAppliedFilters();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults(SearchStringQuery $query)
    {
        if (isset($this->facetGroup)) {
            $this->getConfiguredFilterSetsForFinders($this->facetGroup);
        }
        // get ids and tags from full text search
        $result = $this->query($query->getSearchTerm(), $this->em);

        $ids = array_keys($result);

        //filter the ids if searchParam is not all
        if ($query->getSearchParam() != 'all' && $query->isDropdownFilterEnabled()) {
            $ids = array_intersect(array_keys($result), $this->searchRepository->getProductIdsFromTaxonName($query->getSearchParam()));
        }

        $appliedFilters = $query->getAppliedFilters();
        if (!$appliedFilters) {
            $appliedFilters = array();
        }

        $idsFromAllFacets = null;
        $facets = null;
        if (!empty($ids)) {
            list($facetFilteredIds, $idsFromAllFacets) = $this->getFilteredIds($appliedFilters, $ids);

            if (isset($this->facetGroup)) {
                $facets = $this->calculateNewFacets($result, $facetFilteredIds);
            }
        }

        $paginator = $this->searchRepository->createPaginator(array('id' => $idsFromAllFacets));

        $this->facets = $facets;
        $this->paginator = $paginator;
        $this->filters = $query->getAppliedFilters();

        return $this;
    }

    /**
     * @param $filters
     * @param $ids
     *
     * @return array
     */
    public function getFilteredIds($filters, $ids)
    {
        // Build up lists of product ids for each facet and the total intersect of all for full filtered set
        $facetFilteredIds = array();
        $idsFromAllFacets = $ids;

        foreach ($this->config['filters']['facets'] as $facetName => $facetConfig) {
            if ($thisFacetFilters = $this->getFiltersAppliedForFacet($facetName, $filters)) {
                $facetFilteredIds[$facetName] = $this->getFilteredResults($ids, $thisFacetFilters);
            } else {
                $facetFilteredIds[$facetName] = $ids;
            }

            // Intersect this (possibly filtered) set of products for the facet with the main list
            $idsFromAllFacets = array_intersect($idsFromAllFacets, $facetFilteredIds[$facetName]);
        }

        return array($facetFilteredIds, $idsFromAllFacets);

    }

    /**
     * Returns an array with the new calculated facets
     *
     * @param $result
     * @param $facetFilteredIds
     *
     * @return array
     */
    public function calculateNewFacets($result, $facetFilteredIds)
    {
        $ids = array_keys($result);
        // Fetch the 'options' for each facet based on the limited set of products from the other facets
        $facets = array();

        foreach ($this->config['filters']['facets'] as $facetName => $ormFacet) {

            $idsFromOtherFacets = $ids;
            // Loop around other facets to get the intersect of all of their possibly filtered sets
            foreach ($facetFilteredIds as $otherFacetName => $otherFacetIds) {
                if ($otherFacetName != $facetName) {
                    $idsFromOtherFacets = array_intersect($idsFromOtherFacets, $otherFacetIds);
                }
            }

            $facets[$facetName] = $this->getFacet($idsFromOtherFacets, $this->config['filters']['facets'], $facetName, $result);
        }

        return $facets;
    }

    /**
     * Builds an individual facet category
     *
     * @param $idsFromOtherFacets
     * @param $facets
     * @param $givenFacetName
     * @param $result
     *
     * @return mixed
     */
    public function getFacet($idsFromOtherFacets, $facets, $givenFacetName, $result)
    {
        // gathers the appearance of the elements
        $rawFacets = $this->calculateRawFacets($idsFromOtherFacets, $result);

        // formats the data for sending out the presentation array
        $facetConfig = $facets[$givenFacetName];
        $finalFacets[$givenFacetName] = array();

        if (isset($rawFacets[$givenFacetName])) {

            foreach ($rawFacets[$givenFacetName] as $facet => $count) {

                if (is_numeric($facet) && $facetConfig['type'] == 'range') {

                    foreach ($facetConfig['values'] as $key => $range) {
                        if ($facet >= $range['from'] && $facet <= $range['to']) {
                            if (empty ($finalFacets[$givenFacetName][$key])) {
                                $finalFacets[$givenFacetName][$key] = array('from' => $range['from'], 'to' => $range['to'], 'doc_count' => 1);
                            } else {
                                $finalFacets[$givenFacetName][$key]['doc_count'] += 1;
                            }
                        }

                    }

                    asort($finalFacets[$givenFacetName]);
                } else {
                    $finalFacets[$givenFacetName][] = array('key' => $facet, 'doc_count' => $count);
                }
            }
        }

        return $finalFacets[$givenFacetName];
    }

    /**
     * @param $facetName
     * @param $filters
     *
     * @return array
     */
    public function getFiltersAppliedForFacet($facetName, $filters)
    {
        $filtersForFacet = array();

        foreach ($filters as $filter) {

            $filterName = key($filter);

            if ($facetName == preg_replace('/\d/', '', $filterName)) {
                $filtersForFacet[] = array($filterName => $filter[key($filter)]);
            }
        }

        return $filtersForFacet;
    }

    /**
     * Reduce the result set based on facets
     *
     * @param array $ids
     * @param array $filters
     *
     * @internal param array $facets
     *
     * @return array
     */
    private function getFilteredResults(array $ids, array $filters)
    {
        $filteredIds = array_intersect(
            $this->getFilteredResultsForRange($ids, $filters),
            $this->getFilteredResultsForTerms($ids, $filters)
        );

        return $filteredIds;
    }


    /**
     * @param array $ids
     * @param array $filters
     *
     * @return array
     */
    public function getFilteredResultsForRange(array $ids, array $filters)
    {
        foreach ($filters as $key => $filter) {
            if (strpos($filter[key($filter)], "|") === false) {
                unset($filters[$key]);
            }
        }

        if (empty($filters)) {
            return $ids;
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('product')
            ->from('Sylius\Component\Core\Model\Product', 'product')
            ->leftJoin('product.taxons', 'taxon')
            ->leftJoin('product.attributes', 'attribute')
            ->leftJoin('product.variants', 'variant')
            ->where('product.id IN (:ids)')
            ->setParameter('ids', $ids);

        $orx = $queryBuilder->expr()->orX();


        foreach ($filters as $separateFilter) {

            $filter = $separateFilter[key($separateFilter)];

            if (strpos($filter, "|")) {
                $range = explode("|", $filter);

                $orx->add('variant.price>=' . $range[0] . ' AND variant.price<=' . $range[1] . ' AND variant.master=1');
            } elseif (strpos($filter, "taxon") !== false) {
                $orx->add('taxon.name = \'' . $filter . '\'');
            } elseif (strpos($filter, "_") !== false) {
                $orx->add('attribute.value = \'' . $filter . '\'');
            }
        }

        $queryBuilder->andWhere($orx);

        $results = $queryBuilder->getQuery()->getResult();

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result->getId();
        }

        return $ids;
    }

    /**
     * @param array $ids
     * @param array $filters
     *
     * @return array
     */
    function getFilteredResultsForTerms(array $ids, array $filters)
    {
        foreach ($filters as $key => $filter) {
            if (strpos($filter[key($filter)], "|")) {
                unset($filters[$key]);
            }
        }

        if (empty($filters)) {
            return $ids;
        }

        $result       = array();
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u.itemId, u.tags')
            ->from('Sylius\Bundle\SearchBundle\Entity\SearchIndex', 'u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $ids);

        $res = $queryBuilder->getQuery()->getResult();
        foreach ($res as $facet) {

            foreach ($filters as $separateFilter) {
                $tags = unserialize($facet['tags']);

                if (in_array(ucfirst($separateFilter[key($separateFilter)]), $tags[strtolower(key($separateFilter))])) {
                    $result[$facet['itemId']] = $facet['tags'];
                }
            }
        }

        return array_keys($result);
    }

    /**
     * @param               $searchTerm
     * @param EntityManager $em
     *
     * @return array
     */
    public function query($searchTerm, EntityManager $em)
    {
        $query = $em->createQuery('select u.itemId, u.tags, u.entity from Sylius\Bundle\SearchBundle\Entity\SearchIndex u WHERE MATCH(u.value) AGAINST (:searchTerm) > 0');
        $query->setParameter('searchTerm', $searchTerm);
        $results = $query->getResult();

        $facets = array();
        foreach ($results as $result) {

            if (isset($this->targetIndex) && $result['entity'] != $this->config['orm_indexes'][$this->targetIndex]['class']) {
                continue;
            }

            $facets[$result['itemId']] = $result['tags'];
        }

        return $facets;
    }

    /**
     * @param $filterSetName
     */
    private function getConfiguredFilterSetsForFinders($filterSetName)
    {
        foreach ($this->config['filters']['facets'] as $name => $value) {
            if (!in_array($name, $this->config['filters']['facet_groups'][$filterSetName]['values'])) {
                unset($this->config['filters']['facets'][$name]);
            }
        }

    }

    /**
     * @param $idsFromOtherFacets
     * @param $result
     *
     * @return array
     */
    public function calculateRawFacets($idsFromOtherFacets, $result)
    {
        $rawFacets = array();

        foreach ($result as $id => $serializedTags) {
            if (!in_array($id, $idsFromOtherFacets)) {
                continue;
            }

            $tags = unserialize($serializedTags);
            foreach ($tags as $name => $value) {

                if (is_array($value)) {
                    foreach ($value as $v) {
                        $rawFacets[$name][] = $v;
                    }
                } elseif (is_numeric($value)) {
                    $rawFacets[$name][] = intval($value);
                }
            }
        }

        foreach ($rawFacets as $name => $facet) {
            $rawFacets[$name] = array_count_values($facet);
        }

        return $rawFacets;
    }


} 