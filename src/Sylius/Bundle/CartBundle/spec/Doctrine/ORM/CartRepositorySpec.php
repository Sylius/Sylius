<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Order\Model\OrderInterface;

class CartRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Doctrine\ORM\CartRepository');
    }

    function it_finds_expired_cart(
        $em,
        QueryBuilder $builder,
        AbstractQuery $query,
        Expr $expr,
        CartInterface $cart
    ) {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->lt('o.expiresAt', ':now')->shouldBeCalled()->willReturn($expr);
        $expr->eq('o.state', ':state')->shouldBeCalled()->willReturn($expr);

        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->leftJoin('o.items', 'item')->shouldBeCalled()->willReturn($builder);
        $builder->addSelect('item')->shouldBeCalled()->willReturn($builder);
        $builder->andWhere(Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->andWhere(Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('now', Argument::type(\DateTime::class))->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getResult()->shouldBeCalled()->willReturn([$cart]);

        $this->findExpiredCarts()->shouldReturn([$cart]);
    }
}
