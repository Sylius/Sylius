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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Customer;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Model\ResourceInterface;

final class ShopUserBasedExtensionSpec extends ObjectBehavior
{
    function let(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($sectionProvider, $userContext);
    }

    function it_does_not_apply_conditions_to_collection_for_unsupported_resource(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ResourceInterface::class, new Get());
    }

    function it_does_not_apply_conditions_to_collection_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, CustomerInterface::class, new Get());
    }

    function it_applies_conditions_to_collection(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        ShopUserInterface $user,
        Expr $expr,
        Expr\Func $exprEq,
    ): void {
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn($user);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateJoinAlias('user')->willReturn('user');
        $queryNameGenerator->generateParameterName('user')->willReturn('user');

        $queryBuilder->expr()->willReturn($expr);
        $expr->eq('user.id', ':user')->willReturn($exprEq);
        $queryBuilder->innerJoin('o.user', 'user', 'WITH', $exprEq)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('user', $user)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToCollection($queryBuilder, $queryNameGenerator, CustomerInterface::class, new Get());
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

        $this->applyToItem($queryBuilder, $queryNameGenerator, CustomerInterface::class, [], new Get());
    }

    function it_applies_conditions_to_item(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        ShopUserInterface $user,
        CustomerInterface $customer,
        Expr $expr,
        Expr\Func $exprEq,
    ): void {
        $user->getCustomer()->willReturn($customer);
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn($user);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateJoinAlias('user')->willReturn('user');
        $queryNameGenerator->generateParameterName('user')->willReturn('user');

        $queryBuilder->expr()->willReturn($expr);
        $expr->eq('user.id', ':user')->willReturn($exprEq);
        $queryBuilder->innerJoin('o.user', 'user', 'WITH', $exprEq)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('user', $user)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToItem($queryBuilder, $queryNameGenerator, CustomerInterface::class, [], new Get());
    }
}
