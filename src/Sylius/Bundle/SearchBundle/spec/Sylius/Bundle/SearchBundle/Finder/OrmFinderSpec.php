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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;


/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class OrmFinderSpec extends ObjectBehavior
{
    function let(
        SearchIndexRepository $searchRepository,
        $config,
        $productRepository,
        EntityManager $entityManager,
        QueryLoggerInterface $queryLogger
    )
    {
        $this->beConstructedWith(
            $searchRepository,
            (array)$config,
            $productRepository,
            $entityManager,
            $queryLogger
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Finder\OrmFinder');
    }

    function it_calculates_the_new_facets()
    {
        $idsFromOtherFacets = array(
            0 => 89,
            1 => 67,
            2 => 30,
            3 => 103,
            4 => 40,
            5 => 62,
            6 => 1,
            7 => 42,
            8 => 117,
        );

        $ormFacets = array(
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
        );

        $givenFacetName = 'taxons';

        $result = array(
            89  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:705;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            67  => 'a:4:{s:6:"taxons";a:2:{i:0;s:8:"T-Shirts";i:1;s:9:"SuperTees";}s:5:"price";i:2840;s:7:"made_of";a:1:{i:0;s:9:"Polyester";}s:5:"color";a:3:{i:0;s:3:"Red";i:1;s:4:"Blue";i:2;s:5:"Green";}}',
            30  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:3905;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            103 => 'a:4:{s:6:"taxons";a:2:{i:0;s:8:"T-Shirts";i:1;s:9:"SuperTees";}s:5:"price";i:6222;s:7:"made_of";a:1:{i:0;s:24:"Polyester 10% / Wool 90%";}s:5:"color";a:3:{i:0;s:3:"Red";i:1;s:4:"Blue";i:2;s:5:"Green";}}',
            40  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:4089;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            62  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:5979;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            1   => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:449;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            42  => 'a:4:{s:6:"taxons";a:2:{i:0;s:8:"Stickers";i:1;s:11:"Stickypicky";}s:5:"price";i:8330;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            117 => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:4188;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
        );

        $this->buildFacet($idsFromOtherFacets, $ormFacets, $givenFacetName, $result)->shouldHaveCount(6);

    }

    public function it_calculates_the_raw_facets()
    {
        $idsFromOtherFacets = array(
            0 => 89,
            1 => 67,
            2 => 30,
            3 => 103,
            4 => 40,
            5 => 62,
            6 => 1,
            7 => 42,
            8 => 117,
        );

        $result = array(
            89  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:705;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            67  => 'a:4:{s:6:"taxons";a:2:{i:0;s:8:"T-Shirts";i:1;s:9:"SuperTees";}s:5:"price";i:2840;s:7:"made_of";a:1:{i:0;s:9:"Polyester";}s:5:"color";a:3:{i:0;s:3:"Red";i:1;s:4:"Blue";i:2;s:5:"Green";}}',
            30  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:3905;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            103 => 'a:4:{s:6:"taxons";a:2:{i:0;s:8:"T-Shirts";i:1;s:9:"SuperTees";}s:5:"price";i:6222;s:7:"made_of";a:1:{i:0;s:24:"Polyester 10% / Wool 90%";}s:5:"color";a:3:{i:0;s:3:"Red";i:1;s:4:"Blue";i:2;s:5:"Green";}}',
            40  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:4089;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            62  => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:5979;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            1   => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:449;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            42  => 'a:4:{s:6:"taxons";a:2:{i:0;s:8:"Stickers";i:1;s:11:"Stickypicky";}s:5:"price";i:8330;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
            117 => 'a:4:{s:6:"taxons";a:2:{i:0;s:5:"Books";i:1;s:9:"Bookmania";}s:5:"price";i:4188;s:7:"made_of";a:0:{}s:5:"color";a:0:{}}',
        );

        $this->calculatedFacetContentsFromResults($idsFromOtherFacets, $result)->shouldHaveCount(4);

    }

    public function it_performs_a_fulltext_query(
        EntityManagerInterface $entityManager,
        AbstractQuery $query,
        $result = array()
    )
    {
        $entityManager->createQuery(Argument::any())->shouldBeCalled()->willReturn($query);
        $query->setParameter(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($query);

        $query->getResult()->shouldBeCalled()->willReturn($result);

        $this->query('black', $entityManager)->shouldBeArray();
    }

}
