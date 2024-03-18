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
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider, ['cart']);
    }

    function it_does_not_apply_conditions_to_collection_for_shop(
        QueryBuilder $queryBuilder,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            Request::METHOD_GET,
            [],
        );
    }

    function it_does_not_apply_conditions_to_item_for_shop(
        QueryBuilder $queryBuilder,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

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
        AdminApiSection $adminApiSection,
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
        Expr\Func $exprNotIn,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);

        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');

        $queryBuilder->expr()->willReturn($expr);
        $expr->notIn('o.state', ':state')->willReturn($exprNotIn);
        $queryBuilder->andWhere($exprNotIn)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('state', ['cart'], ArrayParameterType::STRING)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            Request::METHOD_GET,
        );
    }

    function it_applies_conditions_to_item_for_admin(
        AdminApiSection $adminApiSection,
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        Expr $expr,
        Expr\Func $exprNotIn,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $sectionProvider->getSection()->willReturn($adminApiSection);

        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryNameGenerator->generateParameterName('state')->shouldBeCalled()->willReturn('state');

        $queryBuilder->expr()->willReturn($expr);
        $expr->notIn('o.state', ':state')->willReturn($exprNotIn);
        $queryBuilder->andWhere($exprNotIn)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('state', ['cart'], ArrayParameterType::STRING)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            [],
            Request::METHOD_GET,
        );
    }
}
