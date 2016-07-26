<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM;

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
use Sylius\Component\Core\Repository\UserRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class UserRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
    }
    
    function it_implements_user_repository_interface()
    {
        $this->shouldImplement(UserRepositoryInterface::class);
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

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->eq('customer.emailCanonical', ':email')->shouldBeCalled()->willReturn($expr);
        $builder->andWhere($expr)->shouldBeCalled()->willReturn($builder);

        $builder->setParameter('email', 'sylius@example.com')->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();

        $this->findOneByEmail('sylius@example.com');
    }
}
