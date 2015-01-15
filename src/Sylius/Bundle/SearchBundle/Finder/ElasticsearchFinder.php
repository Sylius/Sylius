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

use Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository;
use Sylius\Bundle\SearchBundle\Query\Query;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;

/**
 * Elasticsearch Finder
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchFinder extends AbstractFinder
{
    /**
     * TODO: maybe this should go to configuration, you can use setResultSetSize on the finder object for now
     * @var int
     */
    private $resultSetSize = 100;

    public function __construct(
        SearchIndexRepository $searchRepository,
        $config, $productRepository,
        $targetIndex,
        QueryLoggerInterface $queryLogger
    ) {
        $this->searchRepository = $searchRepository;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->targetIndex = $targetIndex;
        $this->queryLogger = $queryLogger;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setResultSetSize($size)
    {
        $this->resultSetSize = $size;

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

        $elasticaQuery = $this->compileElasticaTaxonQuery(
            $query->getAppliedFilters(),
            $this->config,
            $query->getTaxon()->getName(),
            $this->targetTypes
        );

        $elasticaQuery->setSize($this->resultSetSize);

        $objects = $this->targetIndex->search($elasticaQuery);
        $mapping = $this->targetIndex->getMapping();

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
     * {@inheritdoc}
     */
    public function getResults(SearchStringQuery $query)
    {
        if (isset($this->facetGroup)) {
            $this->initializeFacetGroup($this->facetGroup);
        }

        $elasticaQuery = $this->compileElasticSearchStringQuery(
            $query->getSearchTerm(),
            $query->getAppliedFilters(),
            $this->config,
            $query->getSearchParam(),
            $this->targetTypes
        );

        $elasticaQuery->setSize($this->resultSetSize);

        $objects = $this->targetIndex->search($elasticaQuery);
        $mapping = $this->targetIndex->getMapping();

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
     * @param $configuration
     * @param $taxon
     * @param null $types
     *
     * @return mixed
     */
    public function compileElasticaTaxonQuery($facets = null, $configuration, $taxon, $types = null)
    {
        $elasticaQuery = new \Elastica\Query();
        $boolFilter    = new \Elastica\Filter\Bool();

        if (!empty($types)) {
            foreach ($types as $type) {
                $typeFilter = new \Elastica\Filter\Type($type);
                $boolFilter->addMust($typeFilter);
            }
            $elasticaQuery->setFilter($boolFilter);
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
     * @param null $appliedFilters
     * @param      $configuration
     * @param      $preSearchTaxonFilter
     * @param      $types
     *
     * @return mixed
     */
    public function compileElasticSearchStringQuery($searchTerm, $appliedFilters = null, $configuration, $preSearchTaxonFilter, $types = null)
    {
        $elasticaQuery = new \Elastica\Query();
        $boolFilter    = new \Elastica\Filter\Bool();
        $query = new \Elastica\Query\QueryString($searchTerm);

        if (!empty($types)) {
            foreach ($types as $type) {
                $typeFilter = new \Elastica\Filter\Type($type);
                $boolFilter->addMust($typeFilter);
            }
            $elasticaQuery->setFilter($boolFilter);
        }

        // this is currently the only pre search filter and it's a taxon
        // this should be abstracted out if other types of pre search filters are desired
        if ('all' !== $preSearchTaxonFilter) {
            $query = new \Elastica\Query\Filtered();
            $query->setQuery(new \Elastica\Query\QueryString($searchTerm));

            $taxonFromRequestFilter = new \Elastica\Filter\Terms();
            $taxonFromRequestFilter->setTerms('taxons', array($preSearchTaxonFilter));
            $boolFilter->addMust($taxonFromRequestFilter);
            $elasticaQuery->setFilter($boolFilter);
        }

        $elasticaQuery->setQuery($query);

        return $this->compileElasticsearchQuery($elasticaQuery, $appliedFilters, $configuration);
    }

    /**
     * @param      $elasticaQuery
     * @param null $appliedFilters
     * @param      $configuration
     *
     * @return mixed
     */
    public function compileElasticsearchQuery($elasticaQuery, $appliedFilters = null, $configuration)
    {
        $aggregations = $this->createAggregations($configuration);

        if (!empty($appliedFilters)) {
            list($termFilters, $rangeFilters, $boolFilter, $filters) = $this->applyFilterToElasticaQuery($appliedFilters, $elasticaQuery);

            $aggregations = $this->applyFiltersToIndividualAggregations($appliedFilters, $filters, $rangeFilters, $termFilters, $aggregations, $boolFilter);
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
        $facets = array();
        foreach ($elements->getAggregations() as $name => $facetData) {
            unset($facetData['doc_count']);

            if (isset($facetData[key($facetData)]['buckets'])) {
                $facets[key($facetData)] = $facetData[key($facetData)]['buckets'];
            } else {
                $facets[$name] = $facetData['buckets'];
            }
        }

        return array_reverse($facets);
    }

    /**
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
            if ('all' !== $taxon) {
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
                ${$name.'AggregationFilter'} = new \Elastica\Aggregation\Filter($name);

                ${$name.'Aggregation'} = new \Elastica\Aggregation\Terms($name);
                ${$name.'Aggregation'}->setField($name);
                ${$name.'Aggregation'}->setSize(550);

                ${$name.'AggregationFilter'}->addAggregation(${$name . 'Aggregation'});
            } // range facet creation
            elseif ('range' === $facet['type']) {
                ${$name.'AggregationFilter'} = new \Elastica\Aggregation\Filter($name);

                ${$name.'Aggregation'} = new \Elastica\Aggregation\Range($name);
                foreach ($facet['values'] as $value) {
                    ${$name.'Aggregation'}
                        ->setField($name)
                        ->addRange($value['from'], $value['to']);
                }

                ${$name.'AggregationFilter'}->addAggregation(${$name . 'Aggregation'});
            }

            $aggregations[$name]['aggregation']        = ${$name.'Aggregation'};
            $aggregations[$name]['aggregation_filter'] = ${$name.'AggregationFilter'};
        }

        return $aggregations;
    }

    /**
     * @param $facets
     * @param $appliedFilters
     * @param $rangeFilters
     * @param $termFilters
     * @param $aggregations
     * @param $boolFilter
     *
     * @return array
     */
    public function applyFiltersToIndividualAggregations($facets, $appliedFilters, $rangeFilters, $termFilters, $aggregations, $boolFilter)
    {
        foreach ($facets as $name => $facet) {
            $normName = key($facet);

            ${$normName.'BoolFilter'} = new \Elastica\Filter\Bool();

            foreach ($appliedFilters as $value) {
                if (is_array($value[key($value)])) {
                    ${$normName.'RangeFilter'} = new \Elastica\Filter\Range();

                    foreach ($value as $range) {
                        ${$normName.'RangeFilter'}->addField($name, array('gte' => $range['range'][0], 'lte' => $range['range'][1]));
                        ${$normName.'BoolFilter'}->addMust($rangeFilters);
                    }
                } else {
                    ${$normName.'TermFilter'} = new \Elastica\Filter\Term();
                    ${$normName.'TermFilter'}->setTerm($name, $value[key($value)]);
                    ${$normName.'BoolFilter'}->addMust($termFilters);
                }
            }
        }

        foreach ($this->config['filters']['facets'] as $name => $facet) {
            foreach ($facets as $value) {
                if (count($facets) >= count($this->config['filters']['facets'])) {
                    $aggregations[$name]['aggregation_filter']->setFilter($boolFilter);
                } elseif ($name != key($value)) {
                    if (isset(${key($value).'BoolFilter'})) {
                        $aggregations[$name]['aggregation_filter']->setFilter(${key($value) . 'BoolFilter'});
                    }
                }
            }
        }

        return $aggregations;
    }

    /**
     * @param $appliedFilters
     * @param $elasticaQuery
     *
     * @return array
     */
    public function applyFilterToElasticaQuery($appliedFilters, $elasticaQuery)
    {
        $termFilters  = new \Elastica\Filter\Terms();
        $rangeFilters = new \Elastica\Filter\Range();
        $boolFilter   = new \Elastica\Filter\Bool();

        $filters = array();
        foreach ($appliedFilters as $facet) {
            if (strpos($facet[key($facet)], "|") !== false) {
                $filters[key($facet)][] = array('range' => explode('|', $facet[key($facet)]));
            } else {
                $filters[key($facet)][] = $facet[key($facet)];
            }
        }

        foreach ($filters as $name => $value) {
            if (is_array($value[0])) {
                foreach ($value as $range) {
                    $rangeFilters->addField($name, array('gte' => $range['range'][0], 'lte' => $range['range'][1]));
                    $boolFilter->addShould($rangeFilters);
                }
            } else {
                $termFilters->setTerms($name, $value);
                $boolFilter->addShould($termFilters);
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
