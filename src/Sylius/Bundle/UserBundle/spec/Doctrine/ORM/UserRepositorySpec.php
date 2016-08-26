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
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class UserRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $entityManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
    }

    function it_implements_user_repository_interface()
    {
        $this->shouldImplement(UserRepositoryInterface::class);
    }

    function it_counts_user_user_repository(
        EntityManager $entityManager,
        QueryBuilder $builder,
        \DateTime $from,
        \DateTime $to,
        AbstractQuery $query,
        Expr $expr
    ) {
        $entityManager->createQueryBuilder()->shouldBeCalled()->willReturn($builder);

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
        EntityManager $entityManager,
        QueryBuilder $builder,
        Expr $expr,
        AbstractQuery $query
    ) {
        $entityManager->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->eq('o.emailCanonical', ':email')->shouldBeCalled()->willReturn($expr);
        $builder->andWhere($expr)->shouldBeCalled()->willReturn($builder);

        $builder->setParameter('email', 'sylius@example.com')->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();

        $this->findOneByEmail('sylius@example.com');
    }
}
