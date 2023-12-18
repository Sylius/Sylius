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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext, ['cart']);
    }

    function it_does_not_apply_conditions_to_collection_for_shop_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($shopUser);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            Request::METHOD_GET,
            [],
        );
    }

    function it_does_not_apply_conditions_to_item_for_shop_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            [],
            Request::METHOD_GET,
            [],
        );
    }

    function it_applies_conditions_to_collection_for_admin(
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($adminUser);
        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');
        $queryBuilder->andWhere('o.state != :state')->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('state', ['cart'])->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            Request::METHOD_GET,
        );
    }

    function it_applies_conditions_to_item_for_admin(
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($adminUser);
        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');
        $queryBuilder->andWhere('o.state != :state')->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('state', ['cart'])->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            [],
            Request::METHOD_GET,
        );
    }
}
