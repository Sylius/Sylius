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

use Elastica\Aggregation\Filter;
use Elastica\Aggregation\Range as AggregationRange;
use Elastica\Aggregation\Terms as AggregationTerms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Filtered;
use Elastica\Query\QueryString;
use Elastica\Query\Range as QueryRange;
use Elastica\Query\Term as QueryTerm;
use Elastica\Filter\BoolFilter;
use Elastica\Filter\BoolOr;
use Elastica\Filter\Missing;
use Elastica\Filter\Nested;
use Elastica\Filter\Range as FilterRange;
use Elastica\Filter\Type;
use Elastica\Filter\Term as FilterTerm;
use Elastica\Filter\Terms as FilterTerms;
use Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository;
use Sylius\Bundle\SearchBundle\Query\Query as SyliusSearchQuery;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

/**
 * Elasticsearch Finder
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class ElasticsearchFinder extends AbstractFinder
{
    /**
     * TODO: maybe this should go to configuration, you can use setResultSetSize on the finder object for now
     *
     * @var int
     */
    private $resultSetSize = 100;

    public function __construct(
        SearchIndexRepository $searchRepository,
        $config, $productRepository,
        $targetIndex,
        QueryLoggerInterface $queryLogger,
        ChannelContextInterface $channelContext
    ) {
        $this->searchRepository = $searchRepository;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->targetIndex = $targetIndex;
        $this->queryLogger = $queryLogger;
        $this->channelContext = $channelContext;
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
    public function find(SyliusSearchQuery $queryObject)
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

        $results = [];
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

        $results = [];
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
        $elasticaQuery = new Query();
        $boolFilter = new BoolFilter();

        $this->addAvailableProductsFilters($boolFilter);

        if (!empty($types)) {
            foreach ($types as $type) {
                $typeFilter = new Type($type);
                $boolFilter->addMust($typeFilter);
            }
            $elasticaQuery->setPostFilter($boolFilter);
        }

        if ($channel = $this->channelContext->getChannel()) {
            $channelFilter = new FilterTerms();
            $channelFilter->setTerms('channels', [(string) $channel]);
            $boolFilter->addMust($channelFilter);
            $elasticaQuery->setPostFilter($boolFilter);
        }

        $query = new Filtered();

        $taxonFromRequestFilter = new FilterTerms();
        $taxonFromRequestFilter->setTerms('taxons', [$taxon]);
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
        $elasticaQuery = new Query();
        $boolFilter = new BoolFilter();
        $query = new Filtered();
        $query->setQuery(new QueryString($searchTerm));

        $this->addAvailableProductsFilters($boolFilter);

        if (!empty($types)) {
            foreach ($types as $type) {
                $typeFilter = new Type($type);
                $boolFilter->addMust($typeFilter);
            }
        }

        if ($channel = $this->channelContext->getChannel()) {
            $channelFilter = new FilterTerms();
            $channelFilter->setTerms('channels', [(string) $channel]);
            $boolFilter->addMust($channelFilter);
        }

        // this is currently the only pre search filter and it's a taxon
        // this should be abstracted out if other types of pre search filters are desired
        if ('all' !== $preSearchTaxonFilter) {
            $taxonFromRequestFilter = new FilterTerms();
            $taxonFromRequestFilter->setTerms('taxons', [$preSearchTaxonFilter]);
            $boolFilter->addMust($taxonFromRequestFilter);
        }

        $query->setFilter($boolFilter);
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
     * @param BoolFilter $boolFilter
     */
    public function addAvailableProductsFilters(BoolFilter $boolFilter)
    {
        $enabledFilter = new FilterTerm(array('enabled' => true));
        $boolFilter->addMust($enabledFilter);

        $nestedOrFilter = new BoolOr();
        $this->addAvailableProductsVariantFilters($nestedOrFilter, 'variants');

        $boolFilter->addMust($nestedOrFilter);
    }

    /**
     * @param $elements
     *
     * @return array
     */
    public function transformFacetsForPresentation($elements)
    {
        $facets = [];
        foreach ($elements->getAggregations() as $name => $facetData) {
            unset($facetData['doc_count']);

            if (isset($facetData[key($facetData)]['buckets'])) {
                $facets[key($facetData)] = $facetData[key($facetData)]['buckets'];
            } else {
                $facets[$name] = $facetData['buckets'];
            }
        }

        foreach ($facets as &$facet) {
            $facet = array_filter($facet, function($v){
                return $v["doc_count"] != 0;
            });
        }

        return array_reverse($facets);
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
            $query = new Filtered();

            $taxonFromRequestFilter = new FilterTerms();
            $taxonFromRequestFilter->setTerms('taxons', [$taxon]);
            $boolFilter->addMust($taxonFromRequestFilter);

            $query->setFilter($boolFilter);

            $elasticaQuery->setQuery($query);
        } else {
            if ('all' !== $taxon) {
                $query = new Filtered();
                $query->setQuery(new QueryString($searchTerm));

                $taxonFromRequestFilter = new FilterTerms();
                $taxonFromRequestFilter->setTerms('taxons', [$taxon]);
                $boolFilter->addMust($taxonFromRequestFilter);

                $query->setFilter($boolFilter);
            } else {
                $query = new QueryString($searchTerm);
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
        $aggregations = [];
        foreach ($configuration['filters']['facets'] as $name => $facet) {
            // terms facet creation
            if ($facet['type'] === 'terms') {
                ${$name.'AggregationFilter'} = new Filter($name);

                ${$name.'Aggregation'} = new AggregationTerms($name);
                ${$name.'Aggregation'}->setField($name);
                ${$name.'Aggregation'}->setSize(550);

                ${$name.'AggregationFilter'}->addAggregation(${$name.'Aggregation'});
            } // range facet creation
            elseif ('range' === $facet['type']) {
                ${$name.'AggregationFilter'} = new Filter($name);

                ${$name.'Aggregation'} = new AggregationRange($name);
                foreach ($facet['values'] as $value) {
                    ${$name.'Aggregation'}
                        ->setField($name)
                        ->addRange($value['from'], $value['to']);
                }

                ${$name.'AggregationFilter'}->addAggregation(${$name.'Aggregation'});
            }

            $aggregations[$name]['aggregation'] = ${$name.'Aggregation'};
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

            ${$normName.'BoolFilter'} = new BoolFilter();

            foreach ($appliedFilters as $value) {
                if (is_array($value[key($value)])) {
                    ${$normName.'RangeFilter'} = new FilterRange();

                    foreach ($value as $range) {
                        ${$normName.'RangeFilter'}->addField($name, ['gte' => $range['range'][0], 'lte' => $range['range'][1]]);
                        ${$normName.'BoolFilter'}->addMust($rangeFilters);
                    }
                } else {
                    ${$normName.'TermFilter'} = new FilterTerm();
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
                        $aggregations[$name]['aggregation_filter']->setFilter(${key($value).'BoolFilter'});
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
        $rangeFilters = new BoolOr();
        $boolFilter = new BoolFilter();

        $filters = [];
        $termFilters = [];
        foreach ($appliedFilters as $facet) {
            if (strpos($facet[key($facet)], '|') !== false) {
                $filters[key($facet)][] = ['range' => explode('|', $facet[key($facet)])];
            } else {
                $filters[key($facet)][] = $facet[key($facet)];
            }
        }

        foreach ($filters as $name => $value) {
            if (is_array($value[0])) {
                foreach ($value as $range) {
                    $rangeFilter = new FilterRange();
                    $rangeFilter->addField($name, ['gte' => $range['range'][0], 'lte' => $range['range'][1]]);
                    $rangeFilters->addFilter($rangeFilter);
                }
                $boolFilter->addShould($rangeFilters);
            } else {
                $termFilters = new FilterTerms();
                $termFilters->setTerms($name, $value);
                $boolFilter->addShould($termFilters);
            }
        }

        $elasticaQuery->setFilter($boolFilter);

        return [$termFilters, $rangeFilters, $boolFilter, $filters];
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
     * @param BoolOr $nestedOrFilter Nested or filter
     * @param string $nestedProperty Nested property (can be 'variants')
     */
    private function addAvailableProductsVariantFilters(BoolOr $nestedOrFilter, $nestedProperty)
    {
        $variantsNestedBool = new BoolQuery();
        $variantsNestedBool->setMinimumNumberShouldMatch(1);

        $availableOn = new QueryRange($nestedProperty . '.availableOn', array('lte' => "now"));
        $variantsNestedBool->addMust($availableOn);

        $availableUntil = new Filtered();
        $availableUntilFilter = new BoolOr();

        $availableUntilNull = new Missing($nestedProperty . '.availableUntil');
        $availableUntilFilter->addFilter($availableUntilNull);

        $availableUntilGte = new FilterRange($nestedProperty . '.availableUntil', array('gte' => time()));
        $availableUntilFilter->addFilter($availableUntilGte);

        $availableUntil->setFilter($availableUntilFilter);
        $variantsNestedBool->addMust($availableUntil);

        $availableOnDemand = new QueryTerm(array($nestedProperty . '.availableOnDemand' => true));
        $variantsNestedBool->addShould($availableOnDemand);

        $onHand = new QueryRange($nestedProperty . '.onHand', array('gt' => 0));
        $variantsNestedBool->addShould($onHand);

        $nested = new Nested();
        $nested->setPath($nestedProperty);
        $nested->setQuery($variantsNestedBool);

        $nestedOrFilter->addFilter($nested);
    }
}
