<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShopUser;

final class OrderItemsByVisitorExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_not_apply_to_non_order_item_resources(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_if_user_is_logged_in(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn(new ShopUser());

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderItemInterface::class);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_applies_filters_for_guest_users(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
    ): void {
        $userContext->getUser()->willReturn(null);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateJoinAlias('order')->shouldBeCalled()->willReturn('order');
        $queryNameGenerator->generateJoinAlias('customer')->shouldBeCalled()->willReturn('customer');
        $queryNameGenerator->generateJoinAlias('user')->shouldBeCalled()->willReturn('user');

        $queryBuilder->leftJoin('o.order', 'order')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('order.customer', 'customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('customer.user', 'user')->shouldBeCalled()->willReturn($queryBuilder);

        $queryBuilder
            ->expr()
            ->shouldBeCalled()
            ->willReturn($expr)
        ;

        $expr
            ->isNull('user')
            ->shouldBeCalled()
            ->willReturn('user IS NULL')
        ;

        $expr
            ->eq('order.createdByGuest', ':createdByGuest')
            ->shouldBeCalled()
            ->willReturn('order.createdByGuest = :createdByGuest')
        ;

        $expr
            ->andX(
                'user IS NULL',
                'order.createdByGuest = :createdByGuest',
            )
            ->shouldBeCalled()
            ->willReturn('user IS NULL AND order.createdByGuest = :createdByGuest')
        ;

        $queryBuilder
            ->andWhere('user IS NULL AND order.createdByGuest = :createdByGuest')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->setParameter('createdByGuest', true)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder->andWhere('user IS NULL AND order.createdByGuest = :createdByGuest')->willReturn($queryBuilder);
        $queryBuilder->setParameter('createdByGuest', true)->willReturn($queryBuilder);
        $queryBuilder->addOrderBy('o.id', 'ASC')->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderItemInterface::class);
    }
}
