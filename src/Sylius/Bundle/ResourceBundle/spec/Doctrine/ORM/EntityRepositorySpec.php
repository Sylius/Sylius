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
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
            ->from(Argument::any(), Argument::any(), Argument::cetera())
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
        $this->shouldImplement(RepositoryInterface::class);
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

    function it_applies_criteria_when_finding_one($queryBuilder, Expr $expr)
    {
        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->expr()
                ->shouldBeCalled()
                ->willReturn($expr)
            ;

            $expr
                ->eq('o.'.$property, ':'.$property)
                ->shouldBeCalled()
                ->willReturn('o.'.$property.' = :'.$value)
            ;

            $queryBuilder
                ->andWhere('o.'.$property.' = :'.$value)
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

    function it_applies_criteria_when_finding_by($queryBuilder, Expr $expr)
    {
        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->expr()
                ->shouldBeCalled()
                ->willReturn($expr)
            ;

            $expr
                ->eq('o.'.$property, ':'.$property)
                ->shouldBeCalled()
                ->willReturn('o.'.$property.' = :'.$value)
            ;

            $queryBuilder
                ->andWhere('o.'.$property.' = :'.$value)
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

    function it_applies_criteria_when_finding_by_array($queryBuilder, Expr $expr)
    {
        $criteria = array(
            'baz' => array('foo', 'bar'),
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->expr()
                ->shouldBeCalled()
                ->willReturn($expr)
            ;

            $expr
                ->in('o.'.$property, $value)
                ->shouldBeCalled()
                ->willReturn('o.'.$property.' IN (:'.$property.')')
            ;

            $queryBuilder
                ->andWhere('o.'.$property.' IN (:'.$property.')')
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

    function it_creates_Pagerfanta_paginator()
    {
        $this
            ->createPaginator()
            ->shouldHaveType(Pagerfanta::class)
        ;
    }
}
