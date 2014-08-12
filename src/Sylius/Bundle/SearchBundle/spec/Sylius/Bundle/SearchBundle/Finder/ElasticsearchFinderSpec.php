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
use Sylius\Bundle\SearchBundle\Doctrine\ORM\SyliusSearchIndexRepository;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchFinderSpec extends ObjectBehavior
{
    function let(
        SyliusSearchIndexRepository $searchRepository,
        $config,
        $productRepository,
        $container
    )
    {
        $this->beConstructedWith(
            $searchRepository,
            $config,
            $productRepository,
            $container
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Finder\ElasticsearchFinder');
    }

    function it_compiles_an_elasticsearch_query()
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

        $this->compileElasticsearchQuery('modi', null, $config, null)->shouldHaveType('\Elastica\Query');

    }

}
