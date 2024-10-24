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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Address;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserBasedExtensionSpec extends ObjectBehavior
{
    function let(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($sectionProvider, $userContext);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(QueryCollectionExtensionInterface::class);
        $this->shouldHaveType(QueryItemExtensionInterface::class);
    }

    public function it_does_not_apply_conditions_to_collection_for_unsupported_resource(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    public function it_does_not_apply_conditions_to_item_for_unsupported_resource(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToItem($queryBuilder, $queryNameGenerator, \stdClass::class, []);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_conditions_to_collection_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_conditions_to_item_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);

        $this->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_conditions_to_collection_if_user_is_not_shop_user(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $shopApiSection,
        AdminUserInterface $adminUser,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $userContext->getUser()->willReturn($adminUser);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_conditions_to_item_if_user_is_not_shop_user(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $shopApiSection,
        AdminUserInterface $adminUser,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $userContext->getUser()->willReturn($adminUser);

        $this->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_applies_conditions_to_collection_for_shop_api_section(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $shopApiSection,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
        Expr\Comparison $exprComparison,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $queryNameGenerator->generateParameterName(':customer')->willReturn(':customer');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $expr->eq('o.customer', ':customer')->willReturn($exprComparison);

        $queryBuilder->innerJoin('o.customer', 'customer')->willReturn($queryBuilder);
        $queryBuilder->andWhere($exprComparison)->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);

        $queryBuilder->setParameter(':customer', $customer)->shouldHaveBeenCalledOnce();
    }

    function it_applies_conditions_to_item_for_shop_api_section(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $shopApiSection,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
        Expr\Comparison $exprComparison,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $queryNameGenerator->generateParameterName(':customer')->willReturn(':customer');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->expr()->willReturn($expr);
        $expr->eq('o.customer', ':customer')->willReturn($exprComparison);

        $queryBuilder->innerJoin('o.customer', 'customer')->willReturn($queryBuilder);
        $queryBuilder->andWhere($exprComparison)->willReturn($queryBuilder);

        $this->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);

        $queryBuilder->setParameter(':customer', $customer)->shouldHaveBeenCalledOnce();
    }
}
