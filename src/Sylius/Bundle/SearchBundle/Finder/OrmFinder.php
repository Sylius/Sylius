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
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository;
use Sylius\Bundle\SearchBundle\Model\SearchIndex;
use Sylius\Bundle\SearchBundle\Query\Query;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

/**
 * OrmFinder
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class OrmFinder extends AbstractFinder
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(SearchIndexRepository $searchRepository, $config, $productRepository, EntityManager $em, QueryLoggerInterface $queryLogger, ChannelContextInterface $channelContext)
    {
        $this->searchRepository = $searchRepository;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->em = $em;
        $this->queryLogger = $queryLogger;
        $this->channelContext = $channelContext;
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

        $channel = $this->channelContext->getChannel();

        // First get ALL products from the taxon to get their ids
        $paginator = $this->productRepository->createByTaxonPaginator($query->getTaxon(), ['channels' => $channel]);

        $ids = [];
        $pages = $paginator->getNbPages();
        for ($i = 1; $i <= $pages; ++$i) {
            $paginator->setCurrentPage($i);
            foreach ($paginator->getIterator() as $product) {
                $ids[] = $product->getId();
            }
        }

        // Now apply any filtered facets to reduce the available products
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u.itemId, u.tags, u.entity')
            ->from(SearchIndex::class, 'u')
            ->where('u.itemId IN (:ids)')
            ->setParameter('ids', $ids);
        $indexedItems = $queryBuilder->getQuery()->getResult();

        // TODO: Need to configure / refactor this default!
        $entityName = 'Product';

        $facetsArray = [];

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

        if (count($idsFromAllFacets)) {
            $this->paginator = $this->productRepository->createByTaxonPaginator($query->getTaxon(), ['id' => $idsFromAllFacets, 'channels' => $channel]);
        } else {
            $this->paginator = new Pagerfanta(new ArrayAdapter([]));
        }
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

        $finalResults = $facets = [];
        $modelIdsForChannel = $this->searchRepository->getProductIdsFromChannel($this->channelContext->getChannel());
        // get ids and tags from full text search
        foreach ($this->query($query->getSearchTerm(), $this->em) as $modelClass => $modelIdsToTags) {
            $modelIds = array_keys($modelIdsToTags);

            if (isset($modelIdsForChannel[$modelClass])) {
                $modelIds = array_intersect($modelIds, $modelIdsForChannel[$modelClass]);
            }

            //filter the ids if searchParam is not all
            // TODO: Will refactor pre-search filtering into a service based on the finder configuration
            if ($query->getSearchParam() != 'all' && $query->isDropdownFilterEnabled()) {
                $preFilteredModelIds = $this->searchRepository->getProductIdsFromTaxonName($query->getSearchParam());
                if (isset($preFilteredModelIds[$modelClass])) {
                    $modelIds = array_intersect($modelIds, $preFilteredModelIds[$modelClass]);
                } else {
                    $modelIds = [];
                }
            }

            $appliedFilters = $query->getAppliedFilters() ?: [];

            $finalResults[$modelClass] = [];

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
        $facetFilteredIds = [];
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

        return [$facetFilteredIds, $idsFromAllFacets];
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
        $facets = [];

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
        $calculatedFacets = [];

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
                            $calculatedFacets[$name][$v] = ['key' => $v, 'doc_count' => 1];
                        } else {
                            $calculatedFacets[$name][$v]['doc_count'] += 1;
                        }
                    }
                } elseif (is_numeric($value)) {
                    foreach ($facets[$name]['values'] as $key => $range) {
                        if ($value >= $range['from'] && $value <= $range['to']) {
                            if (empty($calculatedFacets[$name][$key])) {
                                $calculatedFacets[$name][$key] = ['from' => $range['from'], 'to' => $range['to'], 'doc_count' => 1];
                            } else {
                                $calculatedFacets[$name][$key]['doc_count'] += 1;
                            }
                        }
                    }

                    if (isset($calculatedFacets[$name])) {
                        asort($calculatedFacets[$name]);
                    }
                } elseif (is_string($value)) {
                    if (!isset($calculatedFacets[$name][$value])) {
                        $calculatedFacets[$name][$value] = ['key' => $value, 'doc_count' => 1];
                    } else {
                        $calculatedFacets[$name][$value]['doc_count'] += 1;
                    }
                }
            }
        }

        // I want to return an empty array in case this facet has no tags
        if (!isset($calculatedFacets[$givenFacetName])) {
            return [];
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
        $filtersForFacet = [];

        foreach ($filters as $filter) {
            $filterName = key($filter);

            if ($facetName == preg_replace('/\d/', '', $filterName)) {
                $filtersForFacet[] = [$filterName => $filter[$filterName]];
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
    public function getFilteredResults(array $ids, array $filters, $model)
    {
        if (empty($filters)) {
            return $ids;
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u.itemId, u.tags')
            ->from(SearchIndex::class, 'u')
            ->where('u.itemId IN (:ids)')
            ->andWhere('u.entity = :model')
            ->setParameter('ids', $ids)
            ->setParameter('model', $model)
        ;

        $result = [];
        foreach ($queryBuilder->getQuery()->getResult() as $facet) {
            foreach ($filters as $separateFilter) {
                $tags = unserialize($facet['tags']);
                $key = key($separateFilter);

                if ($separateFilter[$key] && $tags[strtolower($key)]) {
                    // range filtering
                    if (is_numeric($tags[strtolower($key)])) {
                        $range = explode('|', $separateFilter[$key]);
                        if ($tags[strtolower($key)] >= $range[0] && $tags[strtolower($key)] <= $range[1]) {
                            $result[] = $facet['itemId'];
                        }
                        // got the value, I don't want to move into more checks
                        continue;
                    }

                    // filtering on an array of values
                    if (is_array($tags[strtolower($key)]) && in_array($separateFilter[$key], $tags[strtolower($key)])) {
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
     * @param string        $searchTerm
     * @param EntityManager $em
     *
     * @return array
     */
    public function query($searchTerm, EntityManager $em)
    {
        $query = $em->createQuery('SELECT u.itemId, u.tags, u.entity FROM Sylius\Bundle\SearchBundle\Model\SearchIndex u WHERE MATCH(u.value) AGAINST (:searchTerm BOOLEAN) > 0');
        $query->setParameter('searchTerm', $searchTerm);

        $elements = [];
        foreach ($query->getResult() as $result) {
            foreach ($this->targetTypes as $type) {
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
