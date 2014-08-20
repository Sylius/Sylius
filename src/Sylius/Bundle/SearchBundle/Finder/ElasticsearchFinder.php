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
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;


/**
 * Elasticsearch Finder
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchFinder implements FinderInterface
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
     * @var
     */
    private $syliusIndex;

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
     * @param                      $syliusIndex
     * @param QueryLoggerInterface $queryLogger
     */
    public function __construct($searchRepository, $config, $productRepository, $syliusIndex, QueryLoggerInterface $queryLogger)
    {
        $this->searchRepository = $searchRepository;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->syliusIndex = $syliusIndex;
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

        $elasticaQuery = $this->compileElasticaTaxonQuery($query->getAppliedFilters(), $this->config, $query->getTaxon()->getName(), 'product');

        $products = $this->syliusIndex->search($elasticaQuery);

        $facets = null;
        if (isset($this->facetGroup)) {
            $facets = $this->transformFacetsForPresentation($products, $query->getAppliedFilters());
        }

        $paginator = $this->productRepository->createByTaxonPaginator(
            $query->getTaxon(), array('id' => $this->getProductIdsFromFulltextSearch($products))
        );

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

        $elasticaQuery = $this->compileElasticSearchStringQuery(
            $query->getSearchTerm(),
            $query->getAppliedFilters(),
            $this->config,
            $query->getSearchParam(),
            $this->targetIndex
        );

        $objects = $this->syliusIndex->search($elasticaQuery);

        $mapping = $this->syliusIndex->getMapping();

        $facets = null;
        if (isset($this->facetGroup)) {
            $facets = $this->transformFacetsForPresentation($objects, $query->getAppliedFilters());
        }

        $results = array();
        foreach ($objects as $object) {
            $results[$mapping[$object->getType()]['_meta']['model']][] = $object->getId();
        }

        $paginator = $this->searchRepository->getArrayPaginator(
            $this->searchRepository->hydrateSearchResults($results)
        );

        $this->facets = $facets;
        $this->paginator = $paginator;
        $this->filters = $query->getAppliedFilters();

        return $this;
    }

    /**
     * @param null $facets
     * @param      $configuration
     * @param      $taxon
     * @param      $type
     *
     * @return mixed
     */
    public function compileElasticaTaxonQuery($facets = null, $configuration, $taxon, $type = null)
    {
        $elasticaQuery = new \Elastica\Query();
        $boolFilter       = new \Elastica\Filter\Bool();

        if ($type) {
            $typeFilter = new \Elastica\Filter\Term();
            $typeFilter->setTerm('_type', $type);
            $boolFilter->addMust($typeFilter);
        }

        $query = new \Elastica\Query\Filtered();

        $taxonFromRequestFilter = new \Elastica\Filter\Terms();
        $taxonFromRequestFilter->setTerms('taxons', array($taxon));
        $boolFilter->addMust($taxonFromRequestFilter);

        $query->setFilter($boolFilter);

        $elasticaQuery->setQuery($query);

        return $this->compileElasticsearchQuery($elasticaQuery, $facets, $configuration);
    }

    /**
     * @param      $searchTerm
     * @param null $facets
     * @param      $configuration
     * @param      $preSearchTaxonFilter
     * @param      $type
     *
     * @return mixed
     */
    public function compileElasticSearchStringQuery($searchTerm, $facets = null, $configuration, $preSearchTaxonFilter, $type = null)
    {
        $elasticaQuery = new \Elastica\Query();
        $boolFilter    = new \Elastica\Filter\Bool();

        if ($type) {
            $typeFilter = new \Elastica\Filter\Term();
            $typeFilter->setTerm('_type', $type);
            $boolFilter->addMust($typeFilter);
        }

        // this is currently the only pre search filter and it's a taxon
        // this should be abstracted out if other types of pre search filters are desired
        if ($preSearchTaxonFilter != 'all') {
            $query = new \Elastica\Query\Filtered();
            $query->setQuery(new \Elastica\Query\QueryString($searchTerm));

            $taxonFromRequestFilter = new \Elastica\Filter\Terms();
            $taxonFromRequestFilter->setTerms('taxons', array($preSearchTaxonFilter));
            $boolFilter->addMust($taxonFromRequestFilter);

            $query->setFilter($boolFilter);
        } else {
            $query = new \Elastica\Query\QueryString($searchTerm);
        }

        $elasticaQuery->setQuery($query);

        return $this->compileElasticsearchQuery($elasticaQuery, $facets, $configuration);
    }

    /**
     * @param      $elasticaQuery
     * @param null $facets
     * @param      $configuration
     *
     * @return mixed
     */
    public function compileElasticsearchQuery($elasticaQuery, $facets = null, $configuration)
    {
        $aggregations = $this->createAggregations($configuration);

        if (!empty($facets)) {

            list($termFilters, $rangeFilters, $boolFilter, $filters) = $this->applyFilterToElasticaQuery($facets, $elasticaQuery);

            $aggregations = $this->applyFiltersToIndividualAggregations($facets, $filters, $rangeFilters, $termFilters, $aggregations, $boolFilter);

        }

        $this->applyAggregationsToElasticaQuery($configuration, $aggregations, $elasticaQuery);

        return $elasticaQuery;
    }

    /**
     * @param $elements
     *
     * @return array
     */
    public function transformFacetsForPresentation($elements)
    {
        $tempFacets = $elements->getAggregations();

        $elasticaFacets = array();

        foreach($tempFacets as $name=>$facetData) {
            unset($facetData['doc_count']);

            if (isset($facetData[key($facetData)]['buckets'])) {
                $elasticaFacets[key($facetData)] = $facetData[key($facetData)]['buckets'];
            } else {
                $elasticaFacets[$name] = $facetData['buckets'];
            }
        }

        return array_reverse($elasticaFacets);
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
     * @param $searchTerm
     * @param $taxon
     * @param $boolFilter
     * @param $elasticaQuery
     */
    public function applyElasticaQueryType($searchTerm, $taxon, $boolFilter, $elasticaQuery)
    {
        if (!$searchTerm) {
            $query = new \Elastica\Query\Filtered();

            $taxonFromRequestFilter = new \Elastica\Filter\Terms();
            $taxonFromRequestFilter->setTerms('taxons', array($taxon));
            $boolFilter->addMust($taxonFromRequestFilter);

            $query->setFilter($boolFilter);

            $elasticaQuery->setQuery($query);

        } else {

            if ($taxon != 'all') {
                $query = new \Elastica\Query\Filtered();
                $query->setQuery(new \Elastica\Query\QueryString($searchTerm));

                $taxonFromRequestFilter = new \Elastica\Filter\Terms();
                $taxonFromRequestFilter->setTerms('taxons', array($taxon));
                $boolFilter->addMust($taxonFromRequestFilter);

                $query->setFilter($boolFilter);
            } else {
                $query = new \Elastica\Query\QueryString($searchTerm);
            }

            $elasticaQuery->setQuery($query);
        }
    }

    /**
     * @param $configuration
     *
     * @return array
     */
    public function createAggregations($configuration)
    {
        $aggregations = array();
        foreach ($configuration['filters']['facets'] as $name => $facet) {

            // terms facet creation
            if ($facet['type'] === 'terms') {
                ${$name . 'AggregationFilter'} = new \Elastica\Aggregation\Filter($name);

                ${$name . 'Aggregation'} = new \Elastica\Aggregation\Terms($name);
                ${$name . 'Aggregation'}->setField($name);
                ${$name . 'Aggregation'}->setSize(550);

                ${$name . 'AggregationFilter'}->addAggregation(${$name . 'Aggregation'});
            }

            // range facet creation
            if ($facet['type'] === 'range') {

                ${$name . 'AggregationFilter'} = new \Elastica\Aggregation\Filter($name);

                ${$name . 'Aggregation'} = new \Elastica\Aggregation\Range($name);
                foreach ($facet['values'] as $value) {
                    ${$name . 'Aggregation'}
                        ->setField($name)
                        ->addRange($value['from'], $value['to']);
                }

                ${$name . 'AggregationFilter'}->addAggregation(${$name . 'Aggregation'});
            }

            $aggregations[$name]['aggregation']        = ${$name . 'Aggregation'};
            $aggregations[$name]['aggregation_filter'] = ${$name . 'AggregationFilter'};
        }

        return $aggregations;
    }

    /**
     * @param $facets
     * @param $filters
     * @param $rangeFilters
     * @param $termFilters
     * @param $aggregations
     * @param $boolFilter
     *
     * @return array
     */
    public function applyFiltersToIndividualAggregations($facets, $filters, $rangeFilters, $termFilters, $aggregations, $boolFilter)
    {
        foreach ($facets as $name => $facet) {

            $normName = key($facet);

            ${$normName . 'BoolFilter'} = new \Elastica\Filter\Bool();

            foreach ($filters as $value) {

                if (is_array($value[key($value)])) {
                    ${$normName . 'RangeFilter'} = new \Elastica\Filter\Range();

                    foreach ($value as $range) {
                        ${$normName . 'RangeFilter'}->addField($name, array('gte' => $range[0], 'lte' => $range[1]));
                        ${$normName . 'BoolFilter'}->addMust($rangeFilters);
                    }
                } else {
                    ${$normName . 'TermFilter'} = new \Elastica\Filter\Term();

                    ${$normName . 'TermFilter'}->setTerm($name, $value[key($value)]);
                    ${$normName . 'BoolFilter'}->addMust($termFilters);
                }
            }

        }

        foreach ($this->config['filters']['facets'] as $name => $facet) {

            foreach ($facets as $value) {

                if (count($facets) >= count($this->config['filters']['facets'])) {
                    $aggregations[$name]['aggregation_filter']->setFilter($boolFilter);

                } elseif ($name != key($value)) {
                    if (isset(${key($value) . 'BoolFilter'})) {
                        $aggregations[$name]['aggregation_filter']->setFilter(${key($value) . 'BoolFilter'});
                    }
                }
            }
        }

        return $aggregations;
    }

    /**
     * @param $facets
     * @param $elasticaQuery
     *
     * @return array
     */
    public function applyFilterToElasticaQuery($facets, $elasticaQuery)
    {
        $termFilters  = new \Elastica\Filter\Term();
        $rangeFilters = new \Elastica\Filter\Range();
        $boolFilter   = new \Elastica\Filter\Bool();

        $filters = array();
        foreach ($facets as $facet) {

            if (strpos($facet[key($facet)], "|") !== false) {
                $filters[key($facet)] = array('ranges' => explode('|', $facet[key($facet)]));
            } else {
                $filters[key($facet)] = array('term' => $facet[key($facet)]);
            }
        }

        foreach ($filters as $name => $value) {

            if (is_array($value[key($value)])) {
                foreach ($value as $range) {
                    $rangeFilters->addField($name, array('gte' => $range[0], 'lte' => $range[1]));
                    $boolFilter->addMust($rangeFilters);
                }
            } else {
                $termFilters->setTerm($name, $value[key($value)]);
                $boolFilter->addMust($termFilters);
            }
        }


        $elasticaQuery->setFilter($boolFilter);

        return array($termFilters, $rangeFilters, $boolFilter, $filters);
    }

    /**
     * @param $configuration
     * @param $aggregations
     * @param $elasticaQuery
     */
    public function applyAggregationsToElasticaQuery($configuration, $aggregations, $elasticaQuery)
    {
        foreach ($configuration['filters']['facets'] as $name => $facet) {

            if (!empty($facet)) {
                $param = $aggregations[$name]['aggregation_filter']->hasParam('filter');

                if ($param) {
                    $elasticaQuery->addAggregation($aggregations[$name]['aggregation_filter']);
                } else {
                    $elasticaQuery->addAggregation($aggregations[$name]['aggregation']);
                }
            } else {

                $elasticaQuery->addAggregation($aggregations[$name]['aggregation']);
            }
        }
    }

}