<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;

class CustomerRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata, FilterCollection $collection)
    {
        $em->getFilters()->willReturn($collection);

        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Doctrine\ORM\CustomerRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository');
    }

    function it_finds_details($em, $collection, QueryBuilder $builder, Expr $expr, AbstractQuery $query)
    {
        $collection->disable('softdeleteable')->shouldBeCalled();

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->eq('o.id', ':id')->shouldBeCalled()->willReturn($expr);

        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);
        $builder->andWhere($expr)->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('id', 1)->shouldBeCalled()->willReturn($builder);
        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();


        $collection->enable('softdeleteable')->shouldBeCalled();

        $this->findForDetailsPage(1);
    }

    function it_creates_paginator(
        $em,
        $collection,
        QueryBuilder $builder,
        Expr $expr,
        AbstractQuery $query
    ) {
        $collection->disable('softdeleteable')->shouldBeCalled();

        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);
        $builder->leftJoin('o.user', 'user')->shouldBeCalled()->willReturn($builder);

        $builder->expr()->willReturn($expr);
        $expr->like(Argument::any(), Argument::any())->willReturn($expr);
        $expr->eq(Argument::any(), Argument::any())->willReturn($expr);

        // enable
        $builder->andWhere(Argument::type('Doctrine\ORM\Query\Expr'))->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('enabled', true)->shouldBeCalled()->willReturn($builder);

        // Query
        $builder->where(Argument::type('Doctrine\ORM\Query\Expr'))->shouldBeCalled()->willReturn($builder);
        $builder->orWhere(Argument::type('Doctrine\ORM\Query\Expr'))->shouldBeCalled()->willReturn($builder);
        $builder->orWhere(Argument::type('Doctrine\ORM\Query\Expr'))->shouldBeCalled()->willReturn($builder);
        $builder->orWhere(Argument::type('Doctrine\ORM\Query\Expr'))->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('query', '%arnaud%')->shouldBeCalled()->willReturn($builder);

        // Sort
        $builder->addOrderBy('o.name', 'asc')->shouldBeCalled();
        $builder->getQuery()->shouldBeCalled()->willReturn($query);

        $this->createFilterPaginator(
            array(
                'enabled' => true,
                'query' => 'arnaud'
            ),
            array('name' => 'asc'),
            true
        )->shouldHaveType('Pagerfanta\Pagerfanta');
    }
}
