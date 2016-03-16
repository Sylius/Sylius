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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class UserRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
    }

    function it_create_paginator(
        $em,
        QueryBuilder $builder,
        Expr $expr,
        AbstractQuery $query
    ) {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);

        $builder->expr()->willReturn($expr);
        $expr->like(Argument::any(), Argument::any())->willReturn($expr);
        $expr->eq(Argument::any(), Argument::any())->willReturn($expr);

        // enable
        $builder->andWhere('o.enabled = :enabled')->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('enabled', true)->shouldBeCalled()->willReturn($builder);

        // Query
        $builder->leftJoin('o.customer', 'customer')->shouldBeCalled()->willReturn($builder);
        $builder->where('customer.emailCanonical LIKE :query')->shouldBeCalled()->willReturn($builder);
        $builder->orWhere('customer.firstName LIKE :query')->shouldBeCalled()->willReturn($builder);
        $builder->orWhere('customer.lastName LIKE :query')->shouldBeCalled()->willReturn($builder);
        $builder->orWhere('o.username LIKE :query')->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('query', '%arnaud%')->shouldBeCalled()->willReturn($builder);

        // Sort
        $builder->addOrderBy('o.name', 'asc')->shouldBeCalled();
        $builder->getQuery()->shouldBeCalled()->willReturn($query);

        $this->createFilterPaginator(
            [
                'enabled' => true,
                'query' => 'arnaud',
            ],
            ['name' => 'asc']
        )->shouldHaveType(Pagerfanta::class);
    }

    function it_finds_details($em, QueryBuilder $builder, Expr $expr, AbstractQuery $query)
    {
        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->eq('o.id', ':id')->shouldBeCalled()->willReturn($expr);

        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->leftJoin('o.customer', 'customer')->shouldBeCalled()->willReturn($builder);
        $builder->addSelect('customer')->shouldBeCalled()->willReturn($builder);
        $builder->where($expr)->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('id', 10)->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();

        $this->findForDetailsPage(10);
    }

    function it_counts_user_user_repository(
        $em,
        QueryBuilder $builder,
        \DateTime $from,
        \DateTime $to,
        AbstractQuery $query,
        Expr $expr
    ) {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->gte(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($expr);
        $expr->lte(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($expr);

        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->andWhere(Argument::type(Expr::class))->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('from', $from)->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('to', $to)->shouldBeCalled()->willReturn($builder);
        $builder->andWhere('o.status = :status')->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('status', 'status')->shouldBeCalled()->willReturn($builder);
        $builder->select('count(o.id)')->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getSingleScalarResult()->shouldBeCalled();

        $this->countBetweenDates($from, $to, 'status');
    }

    function it_finds_one_by_email(
        $em,
        QueryBuilder $builder,
        Expr $expr,
        AbstractQuery $query
    ) {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);

        $builder->leftJoin('o.customer', 'customer')->shouldBeCalled()->willReturn($builder);
        $builder->addSelect('customer')->shouldBeCalled()->willReturn($builder);

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->eq('o.id', ':id')->shouldBeCalled()->willReturn($expr);

        $builder->where($expr)->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('id', 10)->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();

        $this->findForDetailsPage(10);
    }
}
