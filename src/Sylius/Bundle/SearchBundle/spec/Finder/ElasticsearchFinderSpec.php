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

use Elastica\Query;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

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
        QueryLoggerInterface $queryLogger,
        ChannelContextInterface $channelContext
    ) {
        $this->beConstructedWith(
            $searchRepository,
            $config,
            $productRepository,
            $container,
            $queryLogger,
            $channelContext
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Finder\ElasticsearchFinder');
    }

    function it_compiles_a_fulltext_elasticsearch_query()
    {
        $config = [
            'filters' => [
                'facets' => [
                    'taxons' => [
                        'display_name' => 'Basic categories',
                        'type' => 'terms',
                        'value' => null,
                        'values' => [],
                    ],
                    'price' => [
                        'display_name' => 'Available prices',
                        'type' => 'range',
                        'value' => null,
                        'values' => [
                            ['from' => 0, 'to' => 2000],
                            ['from' => 2001, 'to' => 5000],
                            ['from' => 5001, 'to' => 10000],
                        ],
                    ],
                    'made_of' => [
                        'display_name' => 'Material',
                        'type' => 'terms',
                        'value' => null,
                        'values' => [],
                    ],
                    'color' => [
                        'display_name' => 'Available colors',
                        'type' => 'terms',
                        'value' => null,
                        'values' => [],
                    ],
                ],
            ],

        ];

        $this->compileElasticSearchStringQuery('modi', null, $config, 'all', null)->shouldHaveType(Query::class);
    }

    function it_compiles_a_taxon_elasticsearch_query()
    {
        $config = [
            'filters' => [
                'facets' => [
                    'taxons' => [
                        'display_name' => 'Basic categories',
                        'type' => 'terms',
                        'value' => null,
                        'values' => [],
                    ],
                    'price' => [
                        'display_name' => 'Available prices',
                        'type' => 'range',
                        'value' => null,
                        'values' => [
                            ['from' => 0, 'to' => 2000],
                            ['from' => 2001, 'to' => 5000],
                            ['from' => 5001, 'to' => 10000],
                        ],
                    ],
                    'made_of' => [
                        'display_name' => 'Material',
                        'type' => 'terms',
                        'value' => null,
                        'values' => [],
                    ],
                    'color' => [
                        'display_name' => 'Available colors',
                        'type' => 'terms',
                        'value' => null,
                        'values' => [],
                    ],
                ],
            ],

        ];

        $this->compileElasticaTaxonQuery(null, $config, 'T-Shirts', null)->shouldHaveType(Query::class);
    }
}
