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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\User\Model\UserInterface;

final class TaxonItemExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext)
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_not_apply_extension_if_resource_class_is_not_taxon_interface(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ) {
        $this->applyToItem($queryBuilder, $queryNameGenerator, ProductVariantInterface::class, [], null, []);
    }

    function it_does_not_apply_extension_if_user_has_api_access_role(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        UserContextInterface $userContext,
        UserInterface $user,
    ) {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $this->applyToItem($queryBuilder, $queryNameGenerator, TaxonInterface::class, [], null, []);
    }

    function it_applies_extension_to_item_query(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        UserContextInterface $userContext,
    ) {
        $userContext->getUser()->willReturn(null);
        $queryBuilder->getRootAliases()->willReturn(['rootAlias']);
        $queryNameGenerator->generateParameterName('enabled')->willReturn('enabled');
        $queryNameGenerator->generateJoinAlias('child')->willReturn('childAlias');

        $queryBuilder->addSelect('childAlias')->shouldBeCalled();
        $queryBuilder->leftJoin('rootAlias.children', 'childAlias', 'WITH', 'childAlias.enabled = :enabled')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('rootAlias.enabled = :enabled')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('enabled', true)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToItem($queryBuilder, $queryNameGenerator, TaxonInterface::class, [], null, []);
    }
}
