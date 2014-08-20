<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SearchBundle\Finder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;


/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchFinderSpec extends ObjectBehavior
{
    function let(
        SearchIndexRepository $searchRepository,
        $config,
        $productRepository,
        $container,
        QueryLoggerInterface $queryLogger
    )
    {
        $this->beConstructedWith(
            $searchRepository,
            $config,
            $productRepository,
            $container,
            $queryLogger
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Finder\ElasticsearchFinder');
    }

    function it_compiles_a_fulltext_elasticsearch_query()
    {
        $config = array(
            'filters' => array(
                'facets' => array(
                    'taxons'  => array(
                        'display_name' => 'Basic categories',
                        'type'         => 'terms',
                        'value'        => null,
                        'values'       => array(),
                    ),
                    'price'   => array(
                        'display_name' => 'Available prices',
                        'type'         => 'range',
                        'value'        => null,
                        'values'       => array(
                            array('from' => 0, 'to' => 2000),
                            array('from' => 2001, 'to' => 5000),
                            array('from' => 5001, 'to' => 10000),
                        ),
                    ),
                    'made_of' => array(
                        'display_name' => 'Material',
                        'type'         => 'terms',
                        'value'        => null,
                        'values'       => array(),
                    ),
                    'color'   => array(
                        'display_name' => 'Available colors',
                        'type'         => 'terms',
                        'value'        => null,
                        'values'       => array(),
                    ),
                )
            )

        );

        $this->compileElasticSearchStringQuery('modi', null, $config, 'all', null)->shouldHaveType('\Elastica\Query');
    }

    function it_compiles_a_taxon_elasticsearch_query()
    {
        $config = array(
            'filters' => array(
                'facets' => array(
                    'taxons'  => array(
                        'display_name' => 'Basic categories',
                        'type'         => 'terms',
                        'value'        => null,
                        'values'       => array(),
                    ),
                    'price'   => array(
                        'display_name' => 'Available prices',
                        'type'         => 'range',
                        'value'        => null,
                        'values'       => array(
                            array('from' => 0, 'to' => 2000),
                            array('from' => 2001, 'to' => 5000),
                            array('from' => 5001, 'to' => 10000),
                        ),
                    ),
                    'made_of' => array(
                        'display_name' => 'Material',
                        'type'         => 'terms',
                        'value'        => null,
                        'values'       => array(),
                    ),
                    'color'   => array(
                        'display_name' => 'Available colors',
                        'type'         => 'terms',
                        'value'        => null,
                        'values'       => array(),
                    ),
                )
            )

        );

        $this->compileElasticaTaxonQuery(null, $config, 'T-Shirts', null)->shouldHaveType('\Elastica\Query');
    }

}
