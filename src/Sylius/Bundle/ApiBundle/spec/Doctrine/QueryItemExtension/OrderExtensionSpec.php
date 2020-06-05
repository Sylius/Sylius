<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class OrderExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_access_to_get_order_with_null_user_if_present_user_is_null(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        UserContextInterface $userContext
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn(null);

        $queryBuilder->andWhere(sprintf('%s.customer IS NULL', 'o'))->shouldBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            'get',
            []
        );
    }

    function it_access_to_delete_order_with_null_user_if_present_user_is_null(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        UserContextInterface $userContext
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn(null);

        $queryBuilder->andWhere(sprintf('%s.customer IS NULL', 'o'))->shouldBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            'delete',
            []
        );
    }

    function it_access_to_get_order_for_user_that_is_assign_to_this_order(
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator,
        UserContextInterface $userContext
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $queryBuilder
            ->andWhere(sprintf('%s.customer = :customerId', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->setParameter('customerId', 1)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            'get',
            []
        );
    }

    function it_access_to_delete_order_for_user_that_is_assign_to_this_order(
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator,
        UserContextInterface $userContext
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $queryBuilder
            ->andWhere(sprintf('%s.customer = :customerId', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->setParameter('customerId', 1)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            'delete',
            []
        );
    }
}
