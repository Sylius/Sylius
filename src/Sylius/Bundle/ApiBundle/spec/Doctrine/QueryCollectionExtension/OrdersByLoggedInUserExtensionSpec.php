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
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class OrdersByLoggedInUserExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_nothing_if_current_resource_is_not_an_order(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ResourceInterface::class, new Get());
    }

    function it_filters_out_carts_for_all_users(
        UserContextInterface $userContext,
        AdminUserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.state != :state')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderInterface::class, new Get());
    }

    function it_filters_orders_for_shop_user(
        UserContextInterface $userContext,
        ShopUserInterface $user,
        CustomerInterface $customer,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');
        $queryNameGenerator->generateParameterName('customer')->shouldBeCalled()->willReturn('customer');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.state != :state')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('o.customer = :customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('customer', $customer)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderInterface::class, new Get());
    }

    function it_throws_an_access_denied_exception_if_user_is_not_recognised(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.state != :state')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($queryBuilder);

        $this
            ->shouldThrow(AccessDeniedException::class)
            ->during(
                'applyToCollection',
                [
                    $queryBuilder,
                    $queryNameGenerator,
                    OrderInterface::class,
                    new Get(),
                ],
            )
        ;
    }
}
