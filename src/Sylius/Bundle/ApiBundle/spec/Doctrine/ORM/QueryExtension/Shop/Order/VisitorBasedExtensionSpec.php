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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;

final class VisitorBasedExtensionSpec extends ObjectBehavior
{
    function let(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($sectionProvider, $userContext);
    }

    function it_does_not_apply_conditions_to_item_for_unsupported_resource(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem($queryBuilder, $queryNameGenerator, ResourceInterface::class, [], new Get());
    }

    function it_does_not_apply_conditions_to_item_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem($queryBuilder, $queryNameGenerator, OrderInterface::class, [], new Get());
    }

    function it_does_not_apply_conditions_to_item_if_user_is_not_null(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        ShopUserInterface $user,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn($user);
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem($queryBuilder, $queryNameGenerator, OrderInterface::class, [], new Get());
    }

    function it_applies_conditions_to_item(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        CustomerInterface $customer,
        Expr $expr,
        Expr\Func $exprFunc,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn(null);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateJoinAlias('customer')->willReturn('customer');
        $queryNameGenerator->generateJoinAlias('user')->willReturn('user');
        $queryNameGenerator->generateParameterName('createdByGuest')->willReturn('createdByGuest');

        $queryBuilder->leftJoin('o.customer', 'customer')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->leftJoin('customer.user', 'user')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->expr()->willReturn($expr);
        $expr->isNull('user')->willReturn($exprFunc);
        $expr->eq('o.createdByGuest', ':createdByGuest')->willReturn($exprFunc);
        $expr->andX($exprFunc, $exprFunc)->willReturn($exprFunc);

        $queryBuilder->andWhere($exprFunc)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('createdByGuest', true)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToItem($queryBuilder, $queryNameGenerator, OrderInterface::class, [], new Get());
    }
}
