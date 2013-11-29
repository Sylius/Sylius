<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require_once __DIR__.'/../../Fixture/Entity/Foo.php';

/**
 * Doctrine ORM driver entity repository spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class EntityRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $entityManager, ClassMetadata $class, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $class->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo';

        $entityManager
            ->createQueryBuilder()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->select(Argument::any())
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->from(Argument::any(), Argument::any())
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;

        $this->beConstructedWith($entityManager, $class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository');
    }

    function it_implements_Sylius_repository_interface()
    {
        $this->shouldImplement('Sylius\Component\Resource\Repository\RepositoryInterface');
    }

    function it_creates_new_resource_instance()
    {
        $this->createNew()->shouldHaveType('spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo');
    }

    function it_returns_null_if_resource_not_found($queryBuilder, $query)
    {
        $queryBuilder
            ->andWhere('o.id = 3')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $query->getOneOrNullResult()->willReturn(null);

        $this->find(3)->shouldReturn(null);
    }

    function it_applies_criteria_when_finding_one(QueryBuilder $queryBuilder)
    {
        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->andWhere('o.'.$property.' = :'.$property)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->setParameter($property, $value)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;
        }

        $this->findOneBy($criteria)->shouldReturn(null);
    }

    function it_applies_criteria_when_finding_by(QueryBuilder $queryBuilder)
    {
        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->andWhere('o.'.$property.' = :'.$property)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->setParameter($property, $value)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;
        }

        $this->findBy($criteria)->shouldReturn(null);
    }

    function it_returns_null_if_there_are_no_resources()
    {
        $this->findAll()->shouldReturn(null);
    }

    function it_creates_pagerfanta_paginator()
    {
        $this
            ->createPaginator()
            ->shouldHaveType('Pagerfanta\Pagerfanta')
        ;
    }
}
