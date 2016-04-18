<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;

class OrderRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository');
    }

    function it_is_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
        $this->shouldImplement(OrderRepositoryInterface::class);
    }

    function it_finds_recent_orders(
        $em,
        QueryBuilder $builder,
        AbstractQuery $query,
        FilterCollection $filterCollection,
        Expr $expr
    ) {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->isNotNull('o.completedAt')->willReturn($expr);

        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->leftJoin('o.items', 'item')->shouldBeCalled()->willReturn($builder);
        $builder->addSelect('item')->shouldBeCalled()->willReturn($builder);
        $builder->andWhere($expr)->shouldBeCalled()->willReturn($builder);
        $builder->setMaxResults(10)->shouldBeCalled()->willReturn($builder);
        $builder->orderBy('o.completedAt', 'desc')->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getResult()->shouldBeCalled();

        $this->findRecentOrders(10);
    }

    function it_checks_is_the_number_is_used($em, QueryBuilder $builder, AbstractQuery $query)
    {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->select('COUNT(o.id)')->shouldBeCalled()->willReturn($builder);
        $builder->where('o.number = :number')->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('number', 10)->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getSingleScalarResult()->shouldBeCalled();

        $this->isNumberUsed(10);
    }
}
