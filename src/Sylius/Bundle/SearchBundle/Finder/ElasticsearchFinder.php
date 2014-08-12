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
 * Elasticsearch Finder
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchFinder implements FinderInterface
{

    /* @var */
    private $searchRepository;

    /* @var */
    private $config;

    /* @var */
    private $productRepository;

    /* @var */
    private $container;

    /* @var */
    private $facets;

    /* @var */
    private $paginator;

    /* @var */
    private $facetGroup;

    /* @var */
    private $targetIndex;

    /**
     * @param $searchRepository
     * @param $config
     * @param $productRepository
     * @param $container
     */
    public function __construct($searchRepository, $config, $productRepository, $container)
    {
        $this->searchRepository = $searchRepository;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->container = $container;
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
            return $this->getResults($queryObject);

        }  elseif ($queryObject instanceof TaxonQuery) {
            return $this->getResultsForTaxon($queryObject);

        } else {
            throw new Exception("finder can't handle this currently, feel free to implement it!");
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getResultsForTaxon(TaxonQuery $query)
    {
        if (isset($this->facetGroup)) {
            $this->getConfiguredFilterSetsForFinders($this->facetGroup);
        }

        $finder = $this->container->get('fos_elastica.index.sylius.product');

        $elasticaQuery = $this->compileElasticsearchQuery(null, $query->getAppliedFilters(), $this->config, $query->getTaxon()->getName());

        $products = $finder->search($elasticaQuery);

        $facets = null;
        if (isset($this->facetGroup)) {
            $facets = $this->transformFacetsForPresentation($products, $query->getAppliedFilters());
        }

        $paginator = $this->productRepository->createByTaxonPaginator($query->getTaxon(), array('id' => $this->getProductIdsFromFulltextSearch($products)));

        $this->facets = $facets;
        $this->paginator = $paginator;

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

        if (isset($this->targetIndex)) {
            $finder = $this->container->get('fos_elastica.index.sylius.'.$this->targetIndex);
        }else{
            $finder = $this->container->get('fos_elastica.index.sylius');
        }

        $elasticaQuery = $this->compileElasticsearchQuery(
            $query->getSearchTerm(),
            $query->getAppliedFilters(),
            $this->config,
            $query->getSearchParam()
        );

        $products = $finder->search($elasticaQuery);

        $facets = null;
        if (isset($this->facetGroup)) {
            $facets = $this->transformFacetsForPresentation($products, $query->getAppliedFilters());
        }

        $paginator = $this->searchRepository->createPaginator(array('id' => $this->getProductIdsFromFulltextSearch($products)));

        $this->facets = $facets;
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @param      $searchTerm
     * @param null $facets
     * @param      $configuration
     * @param null $taxon
     *
     * @return \Elastica\Query
     */
    public function compileElasticsearchQuery($searchTerm, $facets = null, $configuration, $taxon = null)
    {
        $elasticaQuery = new \Elastica\Query();
        $boolFilter       = new \Elastica\Filter\Bool();

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
                foreach($facet['values'] as $value) {
                    ${$name . 'Aggregation'}
                        ->setField($name)
                        ->addRange($value['from'], $value['to'])
                    ;
                }

                ${$name . 'AggregationFilter'}->addAggregation(${$name . 'Aggregation'});
            }

            if ($facet['type'] === 'attribute') {
                ${$name . 'AggregationFilter'} = new \Elastica\Aggregation\Filter($name);

                ${$name . 'Aggregation'} = new \Elastica\Aggregation\Terms($name);
                ${$name . 'Aggregation'}->setField($name);

                ${$name . 'AggregationFilter'}->addAggregation(${$name . 'Aggregation'});
            }
        }

        if (!empty($facets)) {

            $termFilters      = new \Elastica\Filter\Term();
            $rangeFilters     = new \Elastica\Filter\Range();
            $boolFilter       = new \Elastica\Filter\Bool();

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

            foreach ($facets as $name => $facet) {

                $normName = key($facet);

                ${$normName . 'TermFilter'}      = new \Elastica\Filter\Term();
                ${$normName . 'RangeFilter'}     = new \Elastica\Filter\Range();
                ${$normName . 'BoolFilter'}      = new \Elastica\Filter\Bool();

                foreach ($filters as $value) {

                    if (is_array($value[key($value)])) {
                        foreach ($value as $range) {
                            ${$normName . 'RangeFilter'}->addField($name, array('gte' => $range[0], 'lte' => $range[1]));
                            ${$normName . 'BoolFilter'}->addMust($rangeFilters);
                        }
                    } else {
                        ${$normName . 'TermFilter'}->setTerm($name, $value[key($value)]);
                        ${$normName . 'BoolFilter'}->addMust($termFilters);
                    }
                }

            }

            foreach ($this->config['filters']['facets'] as $name => $facet) {

                foreach ($facets as $value) {

                    if (count($facets)>=count($this->config['filters']['facets'])) {
                        ${$name . 'AggregationFilter'}->setFilter($boolFilter);

                    } elseif ($name!=key($value)) {
                        if (isset(${key($value) . 'BoolFilter'})) {
                            ${$name . 'AggregationFilter'}->setFilter(${key($value) . 'BoolFilter'});
                        }
                    }
                }
            }

        }

        foreach ($configuration['filters']['facets'] as $name => $facet) {

            if (!empty($facet)) {
                $param = ${$name. 'AggregationFilter'}->hasParam('filter');

                if ($param) {
                    $elasticaQuery->addAggregation(${$name. 'AggregationFilter'});
                } else {
                    $elasticaQuery->addAggregation(${$name. 'Aggregation'});
                }
            } else {

                $elasticaQuery->addAggregation(${$name. 'Aggregation'});
            }
        }

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
     * @param $products
     *
     * @return array|null
     */
    private function getProductIdsFromFulltextSearch($products)
    {
        $ids = array();
        foreach ($products as $product) {
            $ids[] = $product->getId();
        }

        if (empty($ids)) {
            return null;
        }

        return $ids;
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

}