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

use PhpSpec\ObjectBehavior;

/**
 * Doctrine ORM driver entity repository spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class EntityRepository extends ObjectBehavior
{
    /**
     * @param Doctrine\ORM\EntityManager         $entityManager
     * @param Doctrine\ORM\Mapping\ClassMetadata $class
     * @param Doctrine\ORM\QueryBuilder          $queryBuilder
     */
    function let($entityManager, $class, $queryBuilder)
    {
        $class->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo';

        $entityManager
            ->createQueryBuilder()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->select(ANY_ARGUMENT)
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->from(ANY_ARGUMENTS)
            ->willReturn($queryBuilder)
        ;

        $this->beConstructedWith($entityManager, $class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository');
    }

    function it_implements_Sylius_repository_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Model\RepositoryInterface');
    }

    function it_creates_new_resource_instance()
    {
        $this->createNew()->shouldHaveType('spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo');
    }

    function it_returns_null_if_resource_not_found()
    {
        $this->find(1)->shouldReturn(null);
    }

    /**
     * @param Doctrine\ORM\QueryBuilder $queryBuilder
     */
    function it_applies_criteria_when_finding_one($queryBuilder)
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

    /**
     * @param Doctrine\ORM\QueryBuilder $queryBuilder
     */
    function it_applies_criteria_when_finding_by($queryBuilder)
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

    function it_creates_Pagerfanta_paginator()
    {
        $this
            ->createPaginator()
            ->shouldHaveType('Pagerfanta\Pagerfanta')
        ;
    }
}
