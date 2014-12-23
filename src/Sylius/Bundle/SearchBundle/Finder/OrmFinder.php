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
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\SearchBundle\Query\Query;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;

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
    private $targetType = array();

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
     * @param $targetType
     *
     * @return $this
     */
    public function addTargetType($targetType)
    {
        $this->targetType[] = $targetType;

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

        throw new \InvalidArgumentException("finder can't handle this currently, feel free to implement it!");
    }

    /**
     * {@inheritdoc}
     */
    public function getResultsForTaxon(TaxonQuery $query)
    {
        if (isset($this->facetGroup)) {
            $this->initializeFacetGroup($this->facetGroup);
        }

        // First get ALL products from the taxon to get their ids
        $paginator = $this->productRepository->createByTaxonPaginator($query->getTaxon(), array());

        $ids = array();
        $pages = $paginator->getNbPages();
        for ($i = 1; $i <= $pages; $i++) {
            $paginator->setCurrentPage($i);
            foreach ($paginator->getIterator() as $product) {
                $ids[] = $product->getId();
            }
        }

        // Now apply any filtered facets to reduce the available products
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u.itemId, u.tags, u.entity')
            ->from('Sylius\Bundle\SearchBundle\Model\SearchIndex', 'u')
            ->where('u.itemId IN (:ids)')
            ->setParameter('ids', $ids);
        $indexedItems = $queryBuilder->getQuery()->getResult();

        // TODO: Need to configure / refactor this default!
        $entityName = "Product";

        $facetsArray = array();

        if ($indexedItems) {
            $entityName = $indexedItems[0]['entity'];
        }

        list($facetFilteredIds, $idsFromAllFacets) = $this->getFilteredIds($query->getAppliedFilters(), $ids, $entityName);

        if (isset($this->facetGroup) && $indexedItems) {
            foreach ($indexedItems as $item) {
                $facetsArray[$item['itemId']] = $item['tags'];
            }
            $this->facets = $this->calculateNewFacets($facetsArray, $facetFilteredIds);
        }

        $this->paginator = $this->productRepository->createByTaxonPaginator($query->getTaxon(), array('id' => $idsFromAllFacets));
        $this->filters = $query->getAppliedFilters();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults(SearchStringQuery $query)
    {
        if (isset($this->facetGroup)) {
            $this->initializeFacetGroup($this->facetGroup);
        }
        // get ids and tags from full text search
        $result = $this->query($query->getSearchTerm(), $this->em);

        $finalResults = array();
        $facets = array();

        foreach ($result as $modelClass => $modelIdsToTags) {

            $modelIds = array_keys($modelIdsToTags);

            //filter the ids if searchParam is not all
            // TODO: Will refactor pre-search filtering into a service based on the finder configuration
            if ($query->getSearchParam() != 'all' && $query->isDropdownFilterEnabled()) {
                $preFilteredModelIds = $this->searchRepository->getProductIdsFromTaxonName($query->getSearchParam());

                if (isset($preFilteredModelIds[$modelClass])) {
                    $modelIds = array_intersect($modelIds, $preFilteredModelIds[$modelClass]);
                }else{
                    $modelIds = array();
                }
            }

            $appliedFilters = $query->getAppliedFilters();
            if (!$appliedFilters) {
                $appliedFilters = array();
            }

            $finalResults[$modelClass] = array();

            if (!empty($modelIds)) {
                list($modelIdsForFacets, $finalResults[$modelClass]) = $this->getFilteredIds($appliedFilters, $modelIds, $modelClass);

                if (isset($this->facetGroup)) {
                    $facets = array_merge_recursive($facets, $this->calculateNewFacets($modelIdsToTags, $modelIdsForFacets));
                }
            }
        }

        $paginator = $this->searchRepository->getArrayPaginator(
            $this->searchRepository->hydrateSearchResults($finalResults)
        );

        $this->facets = $facets;
        $this->paginator = $paginator;
        $this->filters = $query->getAppliedFilters();

        return $this;
    }

    /**
     * @param $filters
     * @param $ids
     * @param $model
     *
     * @return array
     */
    public function getFilteredIds($filters, $ids, $model)
    {
        // Build up lists of model ids for each facet and the total intersect of all for full filtered set
        $facetFilteredIds = array();
        $idsFromAllFacets = $ids;

        foreach ($this->config['filters']['facets'] as $facetName => $facetConfig) {
            if ($thisFacetFilters = $this->getFiltersAppliedForFacet($facetName, $filters)) {
                $facetFilteredIds[$facetName] = $this->getFilteredResults($ids, $thisFacetFilters, $model);
            } else {
                $facetFilteredIds[$facetName] = $ids;
            }

            // Intersect this (possibly filtered) set of model ids for the facet with the main list
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

            $facets[$facetName] = $this->buildFacet($idsFromOtherFacets, $this->config['filters']['facets'], $facetName, $result);
        }

        return $facets;
    }

    /**
     * Builds facets
     *
     * @param $idsFromOtherFacets
     * @param $facets
     * @param $givenFacetName
     * @param $result
     *
     * @return mixed
     */
    public function buildFacet($idsFromOtherFacets, $facets, $givenFacetName, $result)
    {
        /*
         * it is mandatory to build all the facets from scratch since they are always based
         * on the $idsFromOtherFacets
         */
        $calculatedFacets = array();

        foreach ($result as $id => $serializedTags) {
            if (!in_array($id, $idsFromOtherFacets)) {
                continue;
            }

            $tags = unserialize($serializedTags);

            foreach ($tags as $name => $value) {
                if ($name != $givenFacetName) {
                    continue;
                }

                if (is_array($value)) {
                    foreach ($value as $v) {
                        if (!isset($calculatedFacets[$name][$v])) {
                            $calculatedFacets[$name][$v] = array('key' => $v, 'doc_count' => 1);
                        } else {
                            $calculatedFacets[$name][$v]['doc_count'] += 1;
                        }
                    }
                } elseif (is_numeric($value)) {
                    foreach ($facets[$name]['values'] as $key => $range) {
                        if ($value >= $range['from'] && $value <= $range['to']) {
                            if (empty($calculatedFacets[$name][$key])) {
                                $calculatedFacets[$name][$key] = array('from' => $range['from'], 'to' => $range['to'], 'doc_count' => 1);
                            } else {
                                $calculatedFacets[$name][$key]['doc_count'] += 1;
                            }
                        }
                    }
                    asort($calculatedFacets[$name]);
                } elseif (is_string($value)) {
                    if (!isset($calculatedFacets[$name][$value])) {
                        $calculatedFacets[$name][$value] = array('key' => $value, 'doc_count' => 1);
                    } else {
                        $calculatedFacets[$name][$value]['doc_count'] += 1;
                    }
                }
            }
        }

        // I want to return an empty array in case this facet has no tags
        if (!isset($calculatedFacets[$givenFacetName])) {
            return array();
        }

        return $calculatedFacets[$givenFacetName];
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
                $filtersForFacet[] = array($filterName => $filter[$filterName]);
            }
        }

        return $filtersForFacet;
    }

    /**
     * Tag based filtering
     *
     * @param array $ids
     * @param array $filters
     * @param $model
     *
     * @return array
     */
    function getFilteredResults(array $ids, array $filters, $model)
    {
        if (empty($filters)) {
            return $ids;
        }

        $result       = array();
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u.itemId, u.tags')
            ->from('Sylius\Bundle\SearchBundle\Model\SearchIndex', 'u')
            ->where('u.itemId IN (:ids)')
            ->andWhere('u.entity = :model')
            ->setParameter('ids', $ids)
            ->setParameter('model', $model)
        ;

        $res = $queryBuilder->getQuery()->getResult();

        foreach ($res as $facet) {
            foreach ($filters as $separateFilter) {
                $tags = unserialize($facet['tags']);
                $key = key($separateFilter);

                if ($separateFilter[$key] && $tags[strtolower($key)]) {
                    // range filtering
                    if (is_numeric($tags[strtolower($key)])) {
                        $range = explode("|", $separateFilter[$key]);
                        if ($tags[strtolower($key)] >= $range[0] && $tags[strtolower($key)] <= $range[1]) {
                            $result[] = $facet['itemId'];
                        }
                        // got the value, I don't want to move into more checks
                        continue;
                    }

                    // filtering on an array of values
                    if (is_array($tags[strtolower($key)]) && in_array(ucfirst($separateFilter[$key]), $tags[strtolower($key)])) {
                        $result[] = $facet['itemId'];

                        // got the value, I don't want to move into more checks
                        continue;
                    }

                    // filtering on a value
                    if (is_string($tags[strtolower($key)]) && $separateFilter[$key] == $tags[strtolower($key)]) {
                        $result[] = $facet['itemId'];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * The fulltext database query
     *
     * @param               $searchTerm
     * @param EntityManager $em
     *
     * @return array
     */
    public function query($searchTerm, EntityManager $em)
    {
        $query = $em->createQuery('select u.itemId, u.tags, u.entity from Sylius\Bundle\SearchBundle\Model\SearchIndex u WHERE MATCH(u.value) AGAINST (:searchTerm) > 0');
        $query->setParameter('searchTerm', $searchTerm);
        $results = $query->getResult();

        $elements = array();
        foreach ($results as $result) {
            foreach ($this->targetType as $type) {
                if ($result['entity'] != $this->config['orm_indexes'][$type]['class']) {
                    continue;
                }
            }

            $elements[$result['entity']][$result['itemId']] = $result['tags'];
        }

        return $elements;
    }

    /**
     * This functions unsets the configured filters based on the facet groups
     *
     * @param $filterSetName
     */
    private function initializeFacetGroup($filterSetName)
    {
        foreach ($this->config['filters']['facets'] as $name => $value) {
            if (!in_array($name, $this->config['filters']['facet_groups'][$filterSetName]['values'])) {
                unset($this->config['filters']['facets'][$name]);
            }
        }
    }
}
